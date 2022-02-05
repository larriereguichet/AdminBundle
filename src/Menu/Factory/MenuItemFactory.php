<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Menu\Factory;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use LAG\AdminBundle\Admin\Helper\AdminHelperInterface;
use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Contracts\Translation\TranslatorInterface;
use function Symfony\Component\String\u;

class MenuItemFactory implements MenuItemFactoryInterface
{
    public function __construct(
        private FactoryInterface $factory,
        private AdminHelperInterface $adminHelper,
        private RequestStack $requestStack,
        private ApplicationConfiguration $applicationConfiguration,
        private TranslatorInterface $translator,
    )
    {
    }

    public function create(string $name, array $options = []): ItemInterface
    {
        $options = $this->mapRouteParameters($options);

        if (isset($options['admin']) && isset($options['action']) && count($options['children']) === 0) {
            $options['route'] = $this->applicationConfiguration->getRouteName($options['admin'], $options['action']);
        }
        $child = $this->factory->createItem($this->translator->trans($options['text'] ?: $name, [], $this->applicationConfiguration->getTranslationCatalog()), $options);

        if (isset($options['icon'])) {
            $child->setExtra('icon', $options['icon']);
        }
        $currentRoute = $this->requestStack->getCurrentRequest()->get('_route');

        if (($options['route'] ?: null) === $currentRoute) {
            $class = $child->setCurrent(true)->getAttribute('class');
            $child->setAttribute('class', $class.' active');
        }

        foreach ($options['children'] as $name => $childConfiguration) {
            $childItem = $this->create($name, $childConfiguration);
            $child->addChild($childItem);
        }

        return $child;
    }

    private function mapRouteParameters(array $options): array
    {
        if (!$this->adminHelper->hasAdmin()) {
            return $options;
        }
        $admin = $this->adminHelper->getAdmin();
        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        if (empty($options['routeParameters'])) {
            return $options;
        }

        foreach ($options['routeParameters'] as $name => $value) {
            if ($value === null && !u($name)->startsWith('_')) {
                $value = $propertyAccessor->getValue($admin->getData(), $name);
            }
            $options['routeParameters'][$name] = $value;
        }

        return $options;
    }
}

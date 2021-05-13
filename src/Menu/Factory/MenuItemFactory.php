<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Menu\Factory;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use LAG\AdminBundle\Admin\Helper\AdminHelperInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PropertyAccess\PropertyAccess;
use function Symfony\Component\String\u;

class MenuItemFactory implements MenuItemFactoryInterface
{
    private FactoryInterface $factory;
    private AdminHelperInterface $adminHelper;
    private RequestStack $requestStack;

    public function __construct(FactoryInterface $factory, AdminHelperInterface $adminHelper, RequestStack $requestStack)
    {
        $this->factory = $factory;
        $this->adminHelper = $adminHelper;
        $this->requestStack = $requestStack;
    }

    public function create(string $name, array $options = []): ItemInterface
    {
        $options = $this->mapRouteParameters($options);
        $child = $this->factory->createItem($name, $options);

        if (isset($options['icon'])) {
            $child->setExtra('icon', $options['icon']);
        }
        $currentRoute = $this->requestStack->getMasterRequest()->get('_route');

        if (isset($options['route']) && $options['route'] === $currentRoute) {
            $class = $child->setCurrent(true)->getAttribute('class');
            $child->setAttribute('class', $class.' current');
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

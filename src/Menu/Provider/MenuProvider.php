<?php

namespace LAG\AdminBundle\Menu\Provider;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Knp\Menu\Provider\MenuProviderInterface;
use LAG\AdminBundle\Admin\Helper\AdminHelperInterface;
use LAG\AdminBundle\Factory\Configuration\ConfigurationFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Security\Core\Security;
use function Symfony\Component\String\u;

/**
 * Create a new KNP menu using the MenuConfiguration class to validate provided options.
 */
class MenuProvider implements MenuProviderInterface
{
    private FactoryInterface $factory;
    private ConfigurationFactoryInterface $configurationFactory;
    private array $menuConfigurations;
    private RequestStack $requestStack;
    private Security $security;
    private AdminHelperInterface $adminHelper;

    public function __construct(
        array $menuConfigurations,
        FactoryInterface $factory,
        ConfigurationFactoryInterface $configurationFactory,
        RequestStack $requestStack,
        Security $security,
        AdminHelperInterface $adminHelper
    ) {
        $this->factory = $factory;
        $this->configurationFactory = $configurationFactory;
        $this->menuConfigurations = $menuConfigurations;
        $this->requestStack = $requestStack;
        $this->security = $security;
        $this->adminHelper = $adminHelper;
    }

    public function get(string $name, array $options = []): ItemInterface
    {
        $menuConfiguration = $this->configurationFactory->createMenuConfiguration($name, $options)->toArray();
        $menu = $this->factory->createItem('root', [
            'attributes' => $menuConfiguration['attributes'],
            'extras' => $menuConfiguration['extras'],
        ]);
        $currentRoute = $this->requestStack->getMasterRequest()->get('_route');

        foreach ($menuConfiguration['children'] as $itemConfiguration) {
            $itemConfiguration = $this->mapRouteParameters($itemConfiguration);
            $child = $menu->addChild($itemConfiguration['text'], $itemConfiguration);

            if (!empty($itemConfiguration['icon'])) {
                $child->setExtra('icon', $itemConfiguration['icon']);
            }

            if (!empty($itemConfiguration['route']) && $itemConfiguration['route'] === $currentRoute) {
                $class = $child->setCurrent(true)->getAttribute('class');
                $child->setAttribute('class', $class.' current');
            }
        }

        return $menu;
    }

    public function has(string $name, array $options = []): bool
    {
        if (!\array_key_exists($name, $this->menuConfigurations)) {
            return false;
        }
        $configuration = $this->configurationFactory->createMenuConfiguration($name, $options);

        if (!$configuration->hasPermissions()) {
            return true;
        }

        foreach ($configuration->getPermissions() as $permission) {
            if (!$this->security->isGranted($permission, $this->security->getUser())) {
                return false;
            }
        }

        return true;
    }

    private function mapRouteParameters(array $itemConfiguration): array
    {
        if (!$this->adminHelper->hasAdmin()) {
            return $itemConfiguration;
        }
        $admin = $this->adminHelper->getAdmin();
        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        if (empty($itemConfiguration['routeParameters'])) {
            return $itemConfiguration;
        }

        foreach ($itemConfiguration['routeParameters'] as $name => $value) {
            if ($value === null && !u($name)->startsWith('_')) {
                $value = $propertyAccessor->getValue($admin->getData(), $name);
            }
            $itemConfiguration['routeParameters'][$name] = $value;
        }

        return $itemConfiguration;
    }
}

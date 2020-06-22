<?php

namespace LAG\AdminBundle\Bridge\KnpMenu\Provider;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Knp\Menu\Provider\MenuProviderInterface;
use LAG\AdminBundle\Admin\Helper\AdminHelperInterface;
use LAG\AdminBundle\Factory\ConfigurationFactory;
use LAG\AdminBundle\Routing\Resolver\RoutingResolverInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class MenuProvider implements MenuProviderInterface
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var ConfigurationFactory
     */
    private $configurationFactory;

    /**
     * @var RoutingResolverInterface
     */
    private $resolver;

    /**
     * @var array
     */
    private $menuConfigurations;

    /**
     * @var AdminHelperInterface
     */
    private $adminHelper;

    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(
        array $menuConfigurations,
        FactoryInterface $factory,
        ConfigurationFactory $configurationFactory,
        RoutingResolverInterface $resolver,
        AdminHelperInterface $adminHelper,
        RequestStack $requestStack
    ) {
        $this->factory = $factory;
        $this->configurationFactory = $configurationFactory;
        $this->resolver = $resolver;
        $this->menuConfigurations = $menuConfigurations;
        $this->adminHelper = $adminHelper;
        $this->requestStack = $requestStack;
    }

    public function get(string $name, array $options = []): ItemInterface
    {
//        $configuration = [];
//
//        // The application configuration for menus have the lowest priority than the action configuration
//        if (empty($configuration) && !empty($this->menuConfigurations[$name])) {
//            $configuration = $this->menuConfigurations[$name];
//        }
//        $admin = $this->adminHelper->getCurrent();
//
//        // If an action configuration is set, it should override the application configuration
//        if ($admin !== null) {
//            $actionMenusConfiguration = $admin->getAction()->getConfiguration()->get('menus');
//
//            if (!empty($actionMenusConfiguration[$name])) {
//                $configuration = array_merge($options, $actionMenusConfiguration[$name]);
//            }
//        }
//
//        // The given options have the highest priority
//        if (!empty($options)) {
//            $configuration = array_merge($configuration, $options);
//        }
        $menuConfiguration = $this->configurationFactory->createMenuConfiguration($name, $options)->all();
        $menu = $this->factory->createItem('root', [
            'attributes' => $menuConfiguration['attributes'],
        ]);
        $currentRoute = $this->requestStack->getMasterRequest()->get('_route');
        //dump($name, $menuConfiguration);

        foreach ($menuConfiguration['children'] as $itemConfiguration) {
            $child = $menu->addChild($itemConfiguration['text'], $itemConfiguration);
            $child->setExtra('icon', $itemConfiguration['icon']);

            if (!empty($itemConfiguration['route']) && $itemConfiguration['route'] === $currentRoute) {
                $class = $child->setCurrent(true)->getAttribute('class');
                $child->setAttribute('class', $class.' current');
            }
            // TODO move in the bootstrap theme
            $class = $child->getAttribute('class');
            $child->setAttribute('class', $class.' nav-item');

            $linkClass = $child->getLinkAttribute('class');
            $child->setLinkAttribute('class', $linkClass.' nav-link');
        }

        return $menu;
    }

    public function has(string $name, array $options = []): bool
    {
        return key_exists($name, $this->menuConfigurations);
    }

    /**
     * @return ItemInterface[]
     */
    public function all(): array
    {
        $menus = [];

        foreach ($this->menuConfigurations as $menuName => $menuConfiguration) {
            if (!is_array($menuConfiguration)) {
                $menuConfiguration = [];
            }
            $menus[$menuName] = $this->get($menuName, $menuConfiguration);
        }

        return $menus;
    }
}

<?php

namespace LAG\AdminBundle\Factory;

use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Configuration\ApplicationConfigurationStorage;
use LAG\AdminBundle\Configuration\MenuConfiguration;
use LAG\AdminBundle\Configuration\MenuItemConfiguration;
use LAG\AdminBundle\Event\Events;
use LAG\AdminBundle\Event\Menu\MenuEvent;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Menu\Menu;
use LAG\AdminBundle\Menu\MenuItem;
use LAG\AdminBundle\Routing\RoutingLoader;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class MenuFactory
{
    /**
     * @var Menu[]
     */
    private $menus = [];

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var ApplicationConfiguration
     */
    private $applicationConfiguration;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * MenuFactory constructor.
     */
    public function __construct(
        RequestStack $requestStack,
        ApplicationConfigurationStorage $applicationConfigurationStorage,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->requestStack = $requestStack;
        $this->applicationConfiguration = $applicationConfigurationStorage->getConfiguration();
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Create a menu item from a configuration array.
     */
    public function create(string $name, array $configuration): Menu
    {
        $resolver = new OptionsResolver();
        $menuConfiguration = new MenuConfiguration($name, $this->applicationConfiguration->getParameter('title'));
        $menuConfiguration->configureOptions($resolver);

        $menuConfiguration->setParameters($resolver->resolve($configuration));
        $menu = new Menu($name, $menuConfiguration);

        $this->eventDispatcher->dispatch(Events::MENU_CREATE, new MenuEvent($name, $menu));

        foreach ($menuConfiguration->getParameter('items') as $itemName => $item) {
            $menu->addItem($this->createMenuItem($itemName, $item, $menuConfiguration));
        }
        $this->eventDispatcher->dispatch(Events::MENU_CREATED, new MenuEvent($name, $menu));
        $this->menus[$name] = $menu;

        return $menu;
    }

    /**
     * Create a menu item according to the given configuration.
     */
    public function createMenuItem(string $name, array $configuration, MenuConfiguration $parentConfiguration): MenuItem
    {
        // Resolve configuration for the current item
        $resolver = new OptionsResolver();
        $menuItemConfiguration = new MenuItemConfiguration($name, $parentConfiguration->getParameter('position'));
        $menuItemConfiguration->configureOptions($resolver);
        $resolvedConfiguration = $resolver->resolve($configuration);

        if ($this->applicationConfiguration->getParameter('enable_extra_configuration')) {
            $this->addExtraMenuItemConfiguration($resolvedConfiguration);
        }
        $menuItemConfiguration->setParameters($resolvedConfiguration);

        return new MenuItem($menuItemConfiguration);
    }

    /**
     * Return true if the menu exists.
     */
    public function hasMenu(string $name): bool
    {
        return array_key_exists($name, $this->menus);
    }

    /**
     * Return a menu with the given name.
     *
     * @throws Exception
     */
    public function getMenu(string $name): Menu
    {
        if (!$this->hasMenu($name)) {
            throw new Exception('Invalid menu name "'.$name.'"');
        }

        return $this->menus[$name];
    }

    /**
     * Return all the menus.
     *
     * @return Menu[]
     */
    public function getMenus(): array
    {
        return $this->menus;
    }

    private function addExtraMenuItemConfiguration(&$resolvedConfiguration): void
    {
        // Determine the current route to add an active css class if the current item route is the current route
        $route = $this->requestStack->getCurrentRequest()->attributes->get('_route');
        $itemRoute = null;

        // A route string is not required, an admin and an action can also be provided
        if ($resolvedConfiguration['route']) {
            $itemRoute = $resolvedConfiguration['route'];
        } elseif (null !== $resolvedConfiguration['admin']) {
            $itemRoute = RoutingLoader::generateRouteName(
                $resolvedConfiguration['admin'],
                $resolvedConfiguration['action'],
                $this->applicationConfiguration->getParameter('routing_name_pattern')
            );
        }

        // Add an "active" css class dor the current route
        if ($route === $itemRoute) {
            if (!key_exists('class', $resolvedConfiguration['attr'])) {
                $resolvedConfiguration['attr']['class'] = '';
            }
            $resolvedConfiguration['attr']['class'] .= ' active';
        }
    }
}

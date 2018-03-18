<?php

namespace LAG\AdminBundle\Factory;

use LAG\AdminBundle\Configuration\MenuItemConfiguration;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Menu\Menu;
use LAG\AdminBundle\Menu\MenuItem;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MenuFactory
{
    /**
     * @var Menu[]
     */
    private $menus = [];

    /**
     * Create a menu item from a configuration array.
     *
     * @param string $name
     * @param array  $configuration
     *
     * @return Menu
     */
    public function create(string $name, array $configuration)
    {
        $menu = new Menu();

        if (!key_exists('items', $configuration)) {
            return $menu;
        }

        foreach ($configuration['items'] as $item) {
            $menu->addItem($this->createMenuItem($item));
        }
        $this->menus[$name] = $menu;

        return $menu;
    }

    public function createMenuItem(array $configuration): MenuItem
    {
        $resolver = new OptionsResolver();
        $menuItemConfiguration = new MenuItemConfiguration();
        $menuItemConfiguration->configureOptions($resolver);
        $menuItemConfiguration->setParameters($resolver->resolve($configuration));

        return new MenuItem($menuItemConfiguration);
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasMenu(string $name): bool
    {
        return array_key_exists($name, $this->menus);
    }

    /**
     * @param string $name
     *
     * @return Menu
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
     * @return Menu[]
     */
    public function getMenus(): array
    {
        return $this->menus;
    }
}

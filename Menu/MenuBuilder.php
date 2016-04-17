<?php

namespace LAG\AdminBundle\Menu;

use Knp\Menu\ItemInterface;
use LAG\AdminBundle\Menu\Factory\MenuFactory;

/**
 * Create menu handled by the AdminBundle (main, top)
 */
class MenuBuilder
{
    /**
     * @var array
     */
    protected $menusConfiguration;

    /**
     * @var MenuFactory
     */
    protected $menuFactory;

    /**
     * MenuBuilder constructor.
     *
     * @param array $menusConfiguration
     * @param MenuFactory $menuFactory
     */
    public function __construct(
        array $menusConfiguration,
        MenuFactory $menuFactory
    ) {
        $this->menusConfiguration = $menusConfiguration;
        $this->menuFactory = $menuFactory;
    }

    /**
     * Create main menu.
     *
     * @return ItemInterface
     */
    public function mainMenu()
    {
        if (!array_key_exists('main', $this->menusConfiguration)) {
            $this->menusConfiguration['main'] = [];
        }
        $this->menusConfiguration['main']['attr'] = [
            'id' => 'side-menu',
            'class' => 'nav in',
        ];

        return $this
            ->menuFactory
            ->create('main', $this->menusConfiguration['main']);
    }
}

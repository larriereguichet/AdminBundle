<?php

namespace LAG\AdminBundle\Event\Menu;

use LAG\AdminBundle\Menu\Menu;
use Symfony\Component\EventDispatcher\Event;

class MenuEvent extends Event
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var Menu
     */
    private $menu;

    public function __construct(string $name, Menu $menu)
    {
        $this->name = $name;
        $this->menu = $menu;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Menu
     */
    public function getMenu(): Menu
    {
        return $this->menu;
    }
}

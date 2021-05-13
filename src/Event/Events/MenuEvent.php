<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event\Events;

use Knp\Menu\ItemInterface;
use Symfony\Contracts\EventDispatcher\Event;

class MenuEvent extends Event
{
    private string $menuName;
    private ItemInterface $menu;

    public function __construct(string $menuName, ItemInterface $menu)
    {
        $this->menuName = $menuName;
        $this->menu = $menu;
    }

    public function getMenuName(): string
    {
        return $this->menuName;
    }

    public function getMenu(): ItemInterface
    {
        return $this->menu;
    }
}

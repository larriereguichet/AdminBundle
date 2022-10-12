<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event\Events;

use Knp\Menu\ItemInterface;
use Symfony\Contracts\EventDispatcher\Event;

class MenuCreateEvent extends Event
{
    public function __construct(
        private ItemInterface $menu
    ) {
    }

    public function setMenu(ItemInterface $menu): void
    {
        $this->menu = $menu;
    }

    public function getMenu(): ItemInterface
    {
        return $this->menu;
    }
}

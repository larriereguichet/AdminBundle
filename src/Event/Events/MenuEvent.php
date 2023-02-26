<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event\Events;

use Knp\Menu\ItemInterface;
use Symfony\Contracts\EventDispatcher\Event;

class MenuEvent extends Event
{
    public function __construct(
        private ItemInterface $menu
    ) {
    }

    public function getMenu(): ItemInterface
    {
        return $this->menu;
    }
}

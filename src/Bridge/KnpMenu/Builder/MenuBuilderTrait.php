<?php

namespace LAG\AdminBundle\Bridge\KnpMenu\Builder;

use Knp\Menu\ItemInterface;
use LAG\AdminBundle\Event\Events\MenuEvent;
use LAG\AdminBundle\Event\MenuEvents;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

trait MenuBuilderTrait
{
    private EventDispatcherInterface $eventDispatcher;

    public function dispatchMenuEvents(string $menuName, ItemInterface $menu): void
    {
        $this->eventDispatcher->dispatch(new MenuEvent($menuName, $menu), MenuEvents::MENU_CREATED);
        $this->eventDispatcher->dispatch(
            new MenuEvent($menuName, $menu),
            sprintf(MenuEvents::MENU_CREATED_SPECIFIC, $menuName)
        );
    }
}

<?php

namespace LAG\AdminBundle\Bridge\KnpMenu\Builder;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use LAG\AdminBundle\Event\Events\MenuCreatedEvent;
use LAG\AdminBundle\Event\Events\MenuCreateEvent;
use LAG\AdminBundle\Event\MenuEvents;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class UserMenuBuilder
{
    public function __construct(
        private FactoryInterface $factory,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function createMenu(array $options = []): ItemInterface
    {
        $menu = $this->factory->createItem('root');
        $this->eventDispatcher->dispatch($event = new MenuCreateEvent($menu), MenuEvents::MENU_CREATE);
        $this->eventDispatcher->dispatch($event = new MenuCreateEvent($event->getMenu()), sprintf(
            MenuEvents::NAME_EVENT_PATTERN,
            'user',
            'create',
        ));
        $menu = $event->getMenu();
        $menu->addChild('lag_admin.security.logout', [
            'route' => 'lag_admin.logout',
            'extras' => ['icon' => 'sign-out-alt'],
        ]);
        $this->eventDispatcher->dispatch(new MenuCreatedEvent($menu), MenuEvents::MENU_CREATED);
        $this->eventDispatcher->dispatch(new MenuCreateEvent($menu), sprintf(
            MenuEvents::NAME_EVENT_PATTERN,
            'user',
            'created',
        ));

        return $menu;
    }
}

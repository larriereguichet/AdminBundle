<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Bridge\KnpMenu\Builder;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use LAG\AdminBundle\Event\Events\MenuEvent;
use LAG\AdminBundle\Event\MenuEvents;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

abstract class AbstractMenuBuilder implements MenuBuilderInterface
{
    public function __construct(
        private FactoryInterface $factory,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    abstract protected function buildMenu(ItemInterface $menu): void;

    public function build(array $options = []): ItemInterface
    {
        $menu = $this->factory->createItem('root', $options);
        $event = new MenuEvent($menu);

        $this->eventDispatcher->dispatch($event, MenuEvents::MENU_CREATE);
        $this->eventDispatcher->dispatch($event, sprintf(MenuEvents::PRE_EVENT_PATTERN, $this->getName()));

        $this->buildMenu($menu);

        $this->eventDispatcher->dispatch($event, MenuEvents::MENU_CREATED);
        $this->eventDispatcher->dispatch($event, sprintf(MenuEvents::POST_EVENT_PATTERN, $this->getName()));

        return $menu;
    }
}

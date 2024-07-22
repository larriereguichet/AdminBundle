<?php

namespace LAG\AdminBundle\Bridge\KnpMenu\Builder;

use Knp\Menu\ItemInterface;
use Knp\Menu\Provider\MenuProviderInterface;
use LAG\AdminBundle\Event\MenuEvent;
use LAG\AdminBundle\Event\MenuEvents;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final readonly class EventChainProvider implements MenuProviderInterface
{
    public function __construct(
        private MenuProviderInterface $decorated,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function get(string $name, array $options = []): ItemInterface
    {
        $menu = $this->decorated->get($name, $options);
        $event = new MenuEvent($menu);

        $this->eventDispatcher->dispatch($event, MenuEvents::MENU_CREATE);
        $this->eventDispatcher->dispatch($event, sprintf(MenuEvents::PRE_EVENT_PATTERN, $name));

        return $menu;
    }

    public function has(string $name, array $options = []): bool
    {
        return $this->decorated->has($name, $options);
    }
}

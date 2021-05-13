<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Menu\Factory;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use LAG\AdminBundle\Event\Events\MenuEvent;
use LAG\AdminBundle\Event\MenuEvents;
use LAG\AdminBundle\Factory\Configuration\ConfigurationFactoryInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class MenuFactory implements MenuFactoryInterface
{
    private EventDispatcherInterface $eventDispatcher;
    private FactoryInterface $factory;
    private ConfigurationFactoryInterface $configurationFactory;
    private MenuItemFactoryInterface $menuItemFactory;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        FactoryInterface $factory,
        ConfigurationFactoryInterface $configurationFactory,
        MenuItemFactoryInterface $menuItemFactory
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->factory = $factory;
        $this->configurationFactory = $configurationFactory;
        $this->menuItemFactory = $menuItemFactory;
    }

    public function create(string $name, array $options = []): ItemInterface
    {
        $menuConfiguration = $this->configurationFactory->createMenuConfiguration($name, $options)->toArray();
        $menu = $this->factory->createItem('root', [
            'attributes' => $menuConfiguration['attributes'],
            'extras' => $menuConfiguration['extras'],
        ]);
        $event = new MenuEvent($name, $menu);
        $this->eventDispatcher->dispatch($event, MenuEvents::MENU_CREATE);
        $this->eventDispatcher->dispatch($event, sprintf(MenuEvents::MENU_CREATE_SPECIFIC, $name));
        $children = $menuConfiguration['children'] ?? [];

        foreach ($children as $child) {
            $child = $this->menuItemFactory->create($child['text'], $child);
            $menu->addChild($child);
        }
        $this->eventDispatcher->dispatch($event, MenuEvents::MENU_CREATED);
        $this->eventDispatcher->dispatch($event, sprintf(MenuEvents::MENU_CREATED_SPECIFIC, $name));

        return $menu;
    }
}

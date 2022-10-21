<?php

namespace LAG\AdminBundle\Bridge\KnpMenu\Builder;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use LAG\AdminBundle\Event\Events\MenuCreatedEvent;
use LAG\AdminBundle\Event\Events\MenuCreateEvent;
use LAG\AdminBundle\Event\MenuEvents;
use LAG\AdminBundle\Metadata\Index;
use LAG\AdminBundle\Resource\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Routing\Route\RouteNameGeneratorInterface;
use Symfony\Component\String\Inflector\EnglishInflector;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use function Symfony\Component\String\u;

class LeftMenuBuilder
{
    public function __construct(
        private FactoryInterface $factory,
        private ResourceRegistryInterface $resourceRegistry,
        private RouteNameGeneratorInterface $routeNameGenerator,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function createMenu(array $options = []): ItemInterface
    {
        $menu = $this->factory->createItem('root', $options);
        $this->eventDispatcher->dispatch($event = new MenuCreateEvent($menu), MenuEvents::MENU_CREATE);
        $this->eventDispatcher->dispatch($event = new MenuCreateEvent($event->getMenu()), sprintf(
            MenuEvents::NAMED_EVENT_PATTERN,
            'left',
            'create',
        ));
        $menu = $event->getMenu();

        foreach ($this->resourceRegistry->all() as $resource) {
            foreach ($resource->getOperations() as $operation) {
                if (!$operation instanceof Index) {
                    continue;
                }
                $inflector = new EnglishInflector();
                $label = $inflector->pluralize(u($resource->getName())->snake()->toString())[0];
                $menu
                    ->addChild($label, [
                        'route' => $this->routeNameGenerator->generateRouteName($resource, $operation),
                    ])
                    ->setLabel('lag_admin.menu.'.$label)
                ;
            }
        }
        $this->eventDispatcher->dispatch(new MenuCreatedEvent($menu), MenuEvents::MENU_CREATED);
        $this->eventDispatcher->dispatch(new MenuCreateEvent($menu), sprintf(
            MenuEvents::NAMED_EVENT_PATTERN,
            'left',
            'created',
        ));


        return $menu;
    }
}

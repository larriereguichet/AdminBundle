<?php

namespace LAG\AdminBundle\Bridge\KnpMenu\Builder;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use LAG\AdminBundle\Event\Events\MenuCreatedEvent;
use LAG\AdminBundle\Event\Events\MenuCreateEvent;
use LAG\AdminBundle\Event\MenuEvents;
use LAG\AdminBundle\Metadata\Index;
use LAG\AdminBundle\Request\Extractor\ParametersExtractorInterface;
use LAG\AdminBundle\Resource\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Routing\Route\RouteNameGeneratorInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class TopMenuBuilder
{
    public function __construct(
        private ParametersExtractorInterface $parametersExtractor,
        private ResourceRegistryInterface $registry,
        private RequestStack $requestStack,
        private FactoryInterface $factory,
        private RouteNameGeneratorInterface $routeNameGenerator,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function createMenu(array $options = []): ItemInterface
    {
        $menu = $this->factory->createItem('root', $options);
        $request = $this->requestStack->getMainRequest();

        if (!$this->parametersExtractor->supports($request)) {
            return $menu;
        }
        $resourceName = $this->parametersExtractor->getResourceName($request);
        $operationName = $this->parametersExtractor->getOperationName($request);

        $resource = $this->registry->get($resourceName);
        $operation = $resource->getOperation($operationName);

        $this->eventDispatcher->dispatch($event = new MenuCreateEvent($menu), MenuEvents::MENU_CREATE);
        $this->eventDispatcher->dispatch($event = new MenuCreateEvent($event->getMenu()), sprintf(
            MenuEvents::NAMED_EVENT_PATTERN,
            'top',
            'create',
        ));
        $menu = $event->getMenu();

        if (!$operation instanceof Index) {
            return $menu;
        }

        foreach ($operation->getListActions() as $listAction) {
            $route = $listAction->getRouteName();

            if ($route === null) {
                $route = $this
                    ->routeNameGenerator
                    ->generateRouteName($resource, $resource->getOperation($listAction->getOperationName()))
                ;
            }

            $menu->addChild($listAction->getLabel(), [
                'route' => $route,
            ]);
        }
        $this->eventDispatcher->dispatch(new MenuCreatedEvent($menu), MenuEvents::MENU_CREATED);
        $this->eventDispatcher->dispatch(new MenuCreateEvent($menu), sprintf(
            MenuEvents::NAMED_EVENT_PATTERN,
            'top',
            'created',
        ));

        return $menu;
    }
}

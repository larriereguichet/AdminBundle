<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Bridge\KnpMenu\Builder;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use LAG\AdminBundle\Metadata\GetCollection;
use LAG\AdminBundle\Metadata\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Routing\Route\RouteNameGeneratorInterface;
use Symfony\Component\String\Inflector\EnglishInflector;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

use function Symfony\Component\String\u;

class ResourceMenuBuilder extends AbstractMenuBuilder
{
    public function __construct(
        private ResourceRegistryInterface $resourceRegistry,
        private RouteNameGeneratorInterface $routeNameGenerator,
        FactoryInterface $factory,
        EventDispatcherInterface $eventDispatcher,
    ) {
        parent::__construct($factory, $eventDispatcher);
    }

    public function getName(): string
    {
        return 'resource';
    }

    protected function buildMenu(ItemInterface $menu): void
    {
        $inflector = new EnglishInflector();

        if ($menu->hasChildren()) {
            return;
        }

        foreach ($this->resourceRegistry->all() as $resource) {
            foreach ($resource->getOperations() as $operation) {
                if (!$operation instanceof GetCollection) {
                    continue;
                }
                $label = $inflector->pluralize(u($resource->getName())->snake()->toString())[0];
                $route = $this->routeNameGenerator->generateRouteName($resource, $operation);

                $menu
                    ->addChild($label, ['route' => $route])
                    ->setLabel('lag_admin.menu.'.$label)
                ;
            }
        }
    }
}

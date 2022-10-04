<?php

namespace LAG\AdminBundle\Bridge\KnpMenu\Builder;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use LAG\AdminBundle\Admin\Factory\AdminConfigurationFactoryInterface;
use LAG\AdminBundle\Metadata\Index;
use LAG\AdminBundle\Resource\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Routing\Route\RouteNameGeneratorInterface;
use Symfony\Component\String\Inflector\EnglishInflector;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use function Symfony\Component\String\u;

class LeftMenuBuilder
{
    use MenuBuilderTrait;

    public function __construct(
        private FactoryInterface $factory,
        private ResourceRegistryInterface $resourceRegistry,
        private AdminConfigurationFactoryInterface $adminConfigurationFactory,
        private RouteNameGeneratorInterface $routeNameGenerator,
        private EventDispatcherInterface $eventDispatcher,
    )
    {
    }

    public function createMenu(array $options = []): ItemInterface
    {
        $menu = $this->factory->createItem('root', $options);

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
        $this->dispatchMenuEvents('left', $menu);


        return $menu;
    }
}

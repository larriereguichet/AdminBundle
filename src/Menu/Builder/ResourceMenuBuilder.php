<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Menu\Builder;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use LAG\AdminBundle\Resource\Metadata\Index;
use LAG\AdminBundle\Resource\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Routing\Route\RouteNameGeneratorInterface;
use Symfony\Component\String\Inflector\EnglishInflector;

use function Symfony\Component\String\u;

class ResourceMenuBuilder
{
    public function __construct(
        private readonly ResourceRegistryInterface $resourceRegistry,
        private readonly RouteNameGeneratorInterface $routeNameGenerator,
        private readonly FactoryInterface $factory,
    ) {
    }

    public function build(array $options = []): ItemInterface
    {
        $menu = $this->factory->createItem('root', $options);
        $inflector = new EnglishInflector();

        foreach ($this->resourceRegistry->all() as $resource) {
            foreach ($resource->getOperations() as $operation) {
                if (!$operation instanceof Index) {
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

        return $menu;
    }
}

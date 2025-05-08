<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Menu\Builder;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use LAG\AdminBundle\Metadata\Index;
use LAG\AdminBundle\Resource\Factory\DefinitionFactoryInterface;
use LAG\AdminBundle\Resource\Factory\ResourceFactoryInterface;
use LAG\AdminBundle\Routing\Route\RouteNameGeneratorInterface;
use Symfony\Component\String\Inflector\EnglishInflector;

use function Symfony\Component\String\u;

final readonly class ResourceMenuBuilder
{
    public function __construct(
        private DefinitionFactoryInterface $definitionFactory,
        private ResourceFactoryInterface $resourceFactory,
        private RouteNameGeneratorInterface $routeNameGenerator,
        private FactoryInterface $factory,
    ) {
    }

    public function build(array $options = []): ItemInterface
    {
        $inflector = new EnglishInflector();

        $menu = $this->factory->createItem('root', $options);
        $resourceNames = $this->definitionFactory->getResourceNames();

        foreach ($resourceNames as $resourceName) {
            $resource = $this->resourceFactory->create($resourceName);

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

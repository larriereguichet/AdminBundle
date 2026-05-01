<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Bridge\KnpMenu\Builder;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use LAG\AdminBundle\Resource\Factory\ResourceCollectionFactoryInterface;
use LAG\AdminBundle\Routing\Route\RouteNameGeneratorInterface;
use Symfony\Component\String\Inflector\EnglishInflector;
use function Symfony\Component\String\u;

final readonly class ResourceMenuBuilder
{
    public function __construct(
        private ResourceCollectionFactoryInterface $resourceCollectionFactory,
        private RouteNameGeneratorInterface $routeNameGenerator,
        private FactoryInterface $factory,
    ) {
    }

    /** @param array<string, mixed> $options */
    public function build(array $options = []): ItemInterface
    {
        $inflector = new EnglishInflector();

        $menu = $this->factory->createItem('root', $options);
        $resources = $this->resourceCollectionFactory->create();

        foreach ($resources as $resource) {
            $operations = $resource->getCollectionOperations();

            if (\count($operations) === 0) {
                continue;
            }
            $operation = $operations[0];
            // TODO use resource group to group item menu
            $label = $inflector->pluralize(u($resource->getShortName())->snake()->toString())[0];
            $route = $this->routeNameGenerator->generateRouteName($resource, $operation);

            $menu->addChild($label, ['route' => $route])
                ->setLabel('lag_admin.menu.'.$label)
            ;
        }

        return $menu;
    }
}

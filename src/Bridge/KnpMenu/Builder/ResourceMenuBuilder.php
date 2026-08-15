<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Bridge\KnpMenu\Builder;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use LAG\AdminBundle\Metadata\ResourceInterface;
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

        /** @var array<string, ResourceInterface[]> $grouped */
        $grouped = [];
        /** @var ResourceInterface[] $ungrouped */
        $ungrouped = [];

        foreach ($this->resourceCollectionFactory->create() as $resource) {
            if (\count($resource->getCollectionOperations()) === 0) {
                continue;
            }
            $group = $resource->getGroup();

            if ($group !== null) {
                $grouped[$group][] = $resource;
            } else {
                $ungrouped[] = $resource;
            }
        }

        foreach ($grouped as $groupName => $resources) {
            $groupItem = $menu->addChild($groupName)
                ->setLabel('lag_admin.menu.group.'.$groupName)
            ;
            foreach ($resources as $resource) {
                $this->addResourceItem($groupItem, $resource, $inflector);
            }
        }

        foreach ($ungrouped as $resource) {
            $this->addResourceItem($menu, $resource, $inflector);
        }

        return $menu;
    }

    private function addResourceItem(ItemInterface $parent, ResourceInterface $resource, EnglishInflector $inflector): void
    {
        $operations = $resource->getCollectionOperations();
        $operation = array_pop($operations);
        $label = $inflector->pluralize(u($resource->getShortName())->snake()->toString())[0];
        $route = $this->routeNameGenerator->generateRouteName($resource, $operation);

        $parent->addChild($label, ['route' => $route])
            ->setLabel('lag_admin.menu.'.$label)
        ;
    }
}

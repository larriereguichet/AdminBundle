<?php

namespace LAG\AdminBundle\Resource\Locator;

use LAG\AdminBundle\Grid\Registry\GridRegistryInterface;
use LAG\AdminBundle\Metadata\Resource;
use LAG\AdminBundle\Metadata\Grid;

readonly class GridLocator
{
    public function __construct(
        private GridRegistryInterface $registry,
    ) {
    }

    public function locateMetadata(Resource $resource): void
    {
        $reflectionClass = new \ReflectionClass($resource->getDataClass());
        $attributes = $reflectionClass->getAttributes(Grid::class);

        foreach ($attributes as $attribute) {
            $grid = $attribute->newInstance();
            $this->registry->add($grid);
        }
    }
}

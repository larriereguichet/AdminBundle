<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Factory;

use LAG\AdminBundle\DependencyInjection\Locator\ClassLocator;
use LAG\AdminBundle\Metadata\Attribute\Grid;
use LAG\AdminBundle\Metadata\GridMetadataInterface;

final readonly class GridCollectionAttributeMetadataFactory implements GridCollectionMetadataFactoryInterface
{
    /** @param string[] $paths */
    public function __construct(
        private GridCollectionMetadataFactoryInterface $metadataFactory,
        private array $paths,
    ) {
    }

    public function createMetadata(): array
    {
        $grids = $this->metadataFactory->createMetadata();
        $locator = new ClassLocator();

        foreach ($locator->locateClassesByPaths($this->paths) as $resourceClass) {
            $reflectionClass = new \ReflectionClass($resourceClass);
            $attributes = $reflectionClass->getAttributes(Grid::class, \ReflectionAttribute::IS_INSTANCEOF);

            foreach ($attributes as $attribute) {
                /** @var GridMetadataInterface $grid */
                $grid = $attribute->newInstance();

                $grids[$grid->getName()] = $grid;
            }
        }

        return $grids;
    }
}

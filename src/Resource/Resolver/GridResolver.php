<?php

namespace LAG\AdminBundle\Resource\Resolver;

use LAG\AdminBundle\Metadata\Grid;

readonly class GridResolver implements GridResolverInterface
{
    public function __construct(
        private ClassResolverInterface $classResolver,
    ) {
    }

    public function resolveGrids(array $directories): iterable
    {
        foreach ($directories as $directory) {
            $classes = $this->classResolver->resolveClasses($directory);

            foreach ($classes as $class) {
                $attributes = $class->getAttributes(Grid::class);

                foreach ($attributes as $attribute) {
                    yield $attribute->newInstance();
                }
            }
        }
    }
}

<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\Factory;

use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\Grid;

final class CacheGridFactory implements GridFactoryInterface
{
    private array $cache = [];

    public function __construct(
        private readonly GridFactoryInterface $gridFactory,
    ) {
    }

    public function createGrid(CollectionOperationInterface $operation): Grid
    {
        $cacheKey = $operation->getFullName().'.'.$operation->getGrid();

        if (empty($this->cache[$cacheKey])) {
            $this->cache[$cacheKey] = $this->gridFactory->createGrid($operation);
        }

        return $this->cache[$cacheKey];
    }
}

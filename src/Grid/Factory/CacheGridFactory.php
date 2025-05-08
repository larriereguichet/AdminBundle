<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\Factory;

use LAG\AdminBundle\Metadata\Grid;
use LAG\AdminBundle\Metadata\OperationInterface;

final class CacheGridFactory implements GridFactoryInterface
{
    private array $cache = [];

    public function __construct(
        private readonly GridFactoryInterface $gridFactory,
    ) {
    }

    public function createGrid(string $gridName, OperationInterface $operation): Grid
    {
        if (empty($this->cache[$gridName])) {
            $this->cache[$gridName] = $this->gridFactory->createGrid($gridName, $operation);
        }

        return $this->cache[$gridName];
    }
}

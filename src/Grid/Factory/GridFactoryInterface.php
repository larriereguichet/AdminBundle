<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\Factory;

use LAG\AdminBundle\Grid\Grid;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;

interface GridFactoryInterface
{
    public function create(CollectionOperationInterface $operation, iterable $data): Grid;
}

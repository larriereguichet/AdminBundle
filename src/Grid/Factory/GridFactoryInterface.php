<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\Factory;

use LAG\AdminBundle\Grid\GridView;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;

interface GridFactoryInterface
{
    public function create(CollectionOperationInterface $operation, iterable $data): GridView;
}

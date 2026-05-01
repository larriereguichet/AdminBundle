<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\Factory;

use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\GridInterface;

interface GridFactoryInterface
{
    public function create(string $gridName, CollectionOperationInterface $operation): GridInterface;
}

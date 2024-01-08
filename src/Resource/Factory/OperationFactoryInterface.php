<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Factory;

use LAG\AdminBundle\Metadata\OperationInterface;

interface OperationFactoryInterface
{
    public function create(OperationInterface $operation): OperationInterface;
}

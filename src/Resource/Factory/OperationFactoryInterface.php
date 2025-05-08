<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Factory;

use LAG\AdminBundle\Metadata\OperationInterface;

interface OperationFactoryInterface
{
    /**
     * Create an operation from a resource and an application.
     */
    public function create(string $operationName): OperationInterface;
}

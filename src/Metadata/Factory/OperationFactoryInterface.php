<?php

namespace LAG\AdminBundle\Metadata\Factory;

use LAG\AdminBundle\Metadata\AdminResource;
use LAG\AdminBundle\Metadata\OperationInterface;

interface OperationFactoryInterface
{
    public function create(AdminResource $resource, OperationInterface $operation): OperationInterface;
}
<?php

namespace LAG\AdminBundle\Admin\Factory;

use LAG\AdminBundle\Metadata\OperationInterface;

interface OperationFactoryInterface
{
    public function create(OperationInterface $operation): OperationInterface;
}

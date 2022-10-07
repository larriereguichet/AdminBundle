<?php

namespace LAG\AdminBundle\Admin\Factory;

use LAG\AdminBundle\Metadata\Admin;
use LAG\AdminBundle\Metadata\OperationInterface;

interface OperationFactoryInterface
{
    public function create(Admin $resource, OperationInterface $operation): OperationInterface;
}

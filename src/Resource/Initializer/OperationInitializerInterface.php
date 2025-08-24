<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Initializer;

use LAG\AdminBundle\Metadata\Application;
use LAG\AdminBundle\Metadata\OperationInterface;

interface OperationInitializerInterface
{
    public function initializeOperation(Application $application, OperationInterface $operation): OperationInterface;
}

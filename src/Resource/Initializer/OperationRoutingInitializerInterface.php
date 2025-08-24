<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Initializer;

use LAG\AdminBundle\Metadata\Application;
use LAG\AdminBundle\Metadata\OperationInterface;

interface OperationRoutingInitializerInterface
{
    public function initializeOperationRouting(Application $application, OperationInterface $operation): OperationInterface;
}

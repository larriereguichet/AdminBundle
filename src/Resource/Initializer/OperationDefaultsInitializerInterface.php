<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Initializer;

use LAG\AdminBundle\Metadata\Application;
use LAG\AdminBundle\Metadata\OperationInterface;

interface OperationDefaultsInitializerInterface
{
    public function initializeOperationDefaults(Application $application, OperationInterface $operation): OperationInterface;
}

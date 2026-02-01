<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Initializer;

use LAG\AdminBundle\Metadata\Attribute\Application;
use LAG\AdminBundle\Metadata\OperationInterface;

interface OperationInitializerInterface
{
    public function initializeOperation(Application $application, OperationInterface $operation): OperationInterface;
}

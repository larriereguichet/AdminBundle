<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Initializer;

use LAG\AdminBundle\Metadata\Attribute\Application;
use LAG\AdminBundle\Metadata\OperationInterface;

interface OperationFormInitializeInterface
{
    public function initializeOperationForm(Application $application, OperationInterface $operation): OperationInterface;
}

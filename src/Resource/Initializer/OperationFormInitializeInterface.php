<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Initializer;

use LAG\AdminBundle\Metadata\Application;
use LAG\AdminBundle\Metadata\OperationInterface;

interface OperationFormInitializeInterface
{
    public function initializeOperationForm(Application $application, OperationInterface $operation): OperationInterface;
}

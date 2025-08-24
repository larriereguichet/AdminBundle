<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Initializer;

use LAG\AdminBundle\Metadata\Action;
use LAG\AdminBundle\Metadata\OperationInterface;

interface ActionInitializerInterface
{
    public function initializeAction(OperationInterface $operation, Action $action): Action;
}

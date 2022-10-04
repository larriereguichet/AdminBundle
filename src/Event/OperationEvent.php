<?php

namespace LAG\AdminBundle\Event;

use LAG\AdminBundle\Metadata\OperationInterface;
use Symfony\Contracts\EventDispatcher\Event;

class OperationEvent extends Event
{
    const OPERATION_CREATE = 'lag_admin.operation.create';
    const OPERATION_CREATED = 'lag_admin.operation.created';

    public function __construct(
        private OperationInterface $operation,
    ) {
    }

    public function getOperation(): OperationInterface
    {
        return $this->operation;
    }

    public function setOperation(OperationInterface $operation): void
    {
        $this->operation = $operation;
    }
}

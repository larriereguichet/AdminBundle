<?php

namespace LAG\AdminBundle\Event\Events;

use LAG\AdminBundle\Metadata\OperationInterface;
use Symfony\Contracts\EventDispatcher\Event;

class OperationCreateEvent extends Event
{
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

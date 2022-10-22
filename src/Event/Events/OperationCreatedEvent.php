<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event\Events;

use LAG\AdminBundle\Metadata\OperationInterface;
use Symfony\Contracts\EventDispatcher\Event;

class OperationCreatedEvent extends Event
{
    public function __construct(
        private OperationInterface $operation,
    ) {
    }

    public function getOperation(): OperationInterface
    {
        return $this->operation;
    }
}

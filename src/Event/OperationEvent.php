<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event;

use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Metadata\Resource;
use Symfony\Contracts\EventDispatcher\Event;

class OperationEvent extends Event implements ResourceEventInterface
{
    public function __construct(
        private OperationInterface $operation,
    ) {
    }

    public function getResource(): Resource
    {
        return $this->operation->getResource();
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

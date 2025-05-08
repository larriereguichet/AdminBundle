<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event;

use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Metadata\Resource;
use Symfony\Contracts\EventDispatcher\Event;

class DataEvent extends Event implements ResourceEventInterface
{
    public function __construct(
        private readonly mixed $data,
        private readonly OperationInterface $operation,
    ) {
    }

    public function getData(): mixed
    {
        return $this->data;
    }

    public function getOperation(): OperationInterface
    {
        return $this->operation;
    }

    public function getResource(): Resource
    {
        return $this->operation->getResource();
    }
}

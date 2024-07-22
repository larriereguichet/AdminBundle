<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event;

use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use LAG\AdminBundle\Resource\Metadata\Resource;
use Symfony\Contracts\EventDispatcher\Event;

final class DataEvent extends Event
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

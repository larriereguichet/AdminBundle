<?php

namespace LAG\AdminBundle\Tests\State\Processor;

use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use LAG\AdminBundle\State\Processor\ProcessorInterface;

class FakeProcessor implements ProcessorInterface
{
    public function process(mixed $data, OperationInterface $operation, array $uriVariables = [], array $context = []): void
    {
    }
}

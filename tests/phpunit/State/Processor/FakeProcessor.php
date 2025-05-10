<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\State\Processor;

use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\State\Processor\ProcessorInterface;

class FakeProcessor implements ProcessorInterface
{
    public function process(mixed $data, OperationInterface $operation, array $urlVariables = [], array $context = []): void
    {
    }
}

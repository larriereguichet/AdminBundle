<?php

namespace LAG\AdminBundle\State;

use LAG\AdminBundle\Metadata\OperationInterface;

interface DataProcessorInterface
{
    public function process(mixed $data, OperationInterface $operation, array $uriVariables = [], array $context = []): void;
}

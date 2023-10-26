<?php

declare(strict_types=1);

namespace LAG\AdminBundle\State\Processor;

use LAG\AdminBundle\Metadata\OperationInterface;

interface DataProcessorInterface
{
    /**
     * Process given data for the current operation. Data can be persisted in database for example. Variables configured
     * in the operation are provided in the $uriVariables. An additional context could be added.
     *
     * @param mixed $data Data to process
     * @param OperationInterface $operation The current operation according to the current request url and method
     * @param array<string, mixed> $uriVariables Variables (for example identifiers) extracted from the request path
     * @param array<string, mixed> $context Additional context
     */
    public function process(mixed $data, OperationInterface $operation, array $uriVariables = [], array $context = []): void;
}

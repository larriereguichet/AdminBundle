<?php

declare(strict_types=1);

namespace LAG\AdminBundle\State\Processor;

use LAG\AdminBundle\Mapper\ObjectMapperInterface;
use LAG\AdminBundle\Metadata\OperationInterface;

final readonly class MappingProcessor implements ProcessorInterface
{
    public function __construct(
        private ProcessorInterface $processor,
        private ObjectMapperInterface $objectMapper,
    ) {
    }

    public function process(mixed $data, OperationInterface $operation, array $urlVariables = [], array $context = []): void
    {
        if ($operation->getInput() === null || !\is_object($data)) {
            $this->processor->process($data, $operation, $urlVariables, $context);

            return;
        }

        $resourceClass = $operation->getResource()->getResourceClass();
        $mapped = $this->objectMapper->map($data, $resourceClass);

        $this->processor->process($mapped, $operation, $urlVariables, $context);
    }
}

<?php

namespace LAG\AdminBundle\State;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Metadata\OperationInterface;

class CompositeDataProcessor implements DataProcessorInterface
{
    public function __construct(
        /** @var DataProcessorInterface[] $processors */
        private iterable $processors = [],
    ) {
    }

    public function process(mixed $data, OperationInterface $operation, array $uriVariables = [], array $context = []): void
    {
        /** @var DataProcessorInterface $processor */
        foreach ($this->processors as $processor) {
            if ($processor::class === $operation->getProcessor()) {
                $processor->process($data, $operation, $uriVariables, $context);

                return;
            }
        }

        throw new Exception(sprintf(
            'The admin resource "%s" and operation "%s" is not supported by any processor',
            $operation->getResource()->getName(),
            $operation->getName(),
        ));
    }
}

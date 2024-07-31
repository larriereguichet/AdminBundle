<?php

declare(strict_types=1);

namespace LAG\AdminBundle\State\Processor;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;

final readonly class CompositeProcessor implements ProcessorInterface
{
    public function __construct(
        /** @var ProcessorInterface[] $processors */
        private iterable $processors = [],
    ) {
    }

    public function process(mixed $data, OperationInterface $operation, array $uriVariables = [], array $context = []): void
    {
        foreach ($this->processors as $processor) {
            assert($processor instanceof ProcessorInterface);

            if ($processor::class === $operation->getProcessor()) {
                $processor->process($data, $operation, $uriVariables, $context);

                return;
            }
        }

        throw new Exception(sprintf(
            'The resource "%s" and operation "%s" is not supported by any processor',
            $operation->getResource()->getName(),
            $operation->getName()
        ));
    }
}

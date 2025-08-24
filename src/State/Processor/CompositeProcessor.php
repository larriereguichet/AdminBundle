<?php

declare(strict_types=1);

namespace LAG\AdminBundle\State\Processor;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Metadata\OperationInterface;

final readonly class CompositeProcessor implements ProcessorInterface
{
    public function __construct(
        /** @var ProcessorInterface[] $processors */
        private iterable $processors = [],
    ) {
    }

    public function process(mixed $data, OperationInterface $operation, array $urlVariables = [], array $context = []): void
    {
        /** @var ProcessorInterface $processor */
        foreach ($this->processors as $processor) {
            if ($processor::class === $operation->getProcessor()) {
                $processor->process($data, $operation, $urlVariables, $context);

                return;
            }
        }

        throw new Exception(\sprintf(
            'The resource "%s" and operation "%s" is not supported by any processor',
            $operation->getResource()->getName(),
            $operation->getFullName(),
        ));
    }
}

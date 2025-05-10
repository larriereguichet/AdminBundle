<?php

declare(strict_types=1);

namespace LAG\AdminBundle\State\Processor;

use LAG\AdminBundle\Metadata\OperationInterface;

final readonly class PartialAjaxFormProcessor implements ProcessorInterface
{
    public function __construct(
        private ProcessorInterface $processor,
    ) {
    }

    public function process(mixed $data, OperationInterface $operation, array $urlVariables = [], array $context = []): void
    {
        // When a partial form is submitted, we do not handle the form submission to allow a rerender of the form
        if ($context['partial'] === true) {
            return;
        }
        $this->processor->process($data, $operation, $urlVariables, $context);
    }
}

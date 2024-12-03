<?php

declare(strict_types=1);

namespace LAG\AdminBundle\State\Processor;

use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use LAG\AdminBundle\Session\FlashMessageHelperInterface;

final readonly class FlashMessageProcessor implements ProcessorInterface
{
    public function __construct(
        private ProcessorInterface $processor,
        private FlashMessageHelperInterface $flashMessageHelper,
    ) {
    }

    public function process(mixed $data, OperationInterface $operation, array $uriVariables = [], array $context = []): void
    {
        $this->processor->process($data, $operation, $uriVariables, $context);

        if ($operation->getSuccessMessage() === null) {
            return;
        }
        $this->flashMessageHelper->success($operation->getSuccessMessage());
    }
}

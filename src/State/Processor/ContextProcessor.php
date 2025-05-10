<?php

declare(strict_types=1);

namespace LAG\AdminBundle\State\Processor;

use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Request\ContextBuilder\ContextBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final readonly class ContextProcessor implements ProcessorInterface
{
    public function __construct(
        private ProcessorInterface $processor,
        private RequestStack $requestStack,
        private ContextBuilderInterface $contextBuilder,
    ) {
    }

    public function process(mixed $data, OperationInterface $operation, array $urlVariables = [], array $context = []): void
    {
        $request = $this->requestStack->getCurrentRequest();
        $context += $this->contextBuilder->buildContext($operation, $request);

        $this->processor->process($data, $operation, $urlVariables, $context);
    }
}

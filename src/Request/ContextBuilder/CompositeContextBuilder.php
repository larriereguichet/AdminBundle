<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Request\ContextBuilder;

use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use Symfony\Component\HttpFoundation\Request;

final readonly class CompositeContextBuilder implements ContextBuilderInterface
{
    public function __construct(
        private iterable $contextBuilders,
    ) {
    }

    public function supports(OperationInterface $operation, Request $request): bool
    {
        foreach ($this->contextBuilders as $contextBuilder) {
            if ($contextBuilder->supports($operation, $request)) {
                return true;
            }
        }

        return false;
    }

    public function buildContext(OperationInterface $operation, Request $request): array
    {
        $context = [];

        foreach ($this->contextBuilders as $contextBuilder) {
            if ($contextBuilder->supports($operation, $request)) {
                $context += $contextBuilder->buildContext($operation, $request);
            }
        }

        return $context;
    }
}
<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Request\Context;

use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use Symfony\Component\HttpFoundation\Request;

class CompositeContextProvider implements ContextProviderInterface
{
    public function __construct(
        /** @var iterable<int, ContextProviderInterface> $contextProviders */
        private iterable $contextProviders,
    ) {
    }

    public function getContext(OperationInterface $operation, Request $request): array
    {
        $context = [];

        foreach ($this->contextProviders as $contextProvider) {
            $context += $contextProvider->getContext($operation, $request);
        }

        return $context;
    }
}

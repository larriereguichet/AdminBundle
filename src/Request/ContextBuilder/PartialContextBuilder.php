<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Request\ContextBuilder;

use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use Symfony\Component\HttpFoundation\Request;

final readonly class PartialContextBuilder implements ContextBuilderInterface
{
    public function __construct(
        private ContextBuilderInterface $contextBuilder
    ) {
    }

    public function buildContext(OperationInterface $operation, Request $request): array
    {
        $context = $this->contextBuilder->buildContext($operation, $request);
        $context['partial'] = false;

        if ($request->query->getBoolean('_partial') === true) {
            $context['partial'] = true;
        }

        return $context;
    }
}
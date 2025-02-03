<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Request\ContextBuilder;

use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use Symfony\Component\HttpFoundation\Request;

final readonly class OperationContextBuilder implements ContextBuilderInterface
{
    public function supports(OperationInterface $operation, Request $request): bool
    {
        return true;
    }

    public function buildContext(OperationInterface $operation, Request $request): array
    {
        return $operation->getContext();
    }
}

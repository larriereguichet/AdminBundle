<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Request\ContextBuilder;

use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\OperationInterface;
use Symfony\Component\HttpFoundation\Request;

final readonly class PaginationContextBuilder implements ContextBuilderInterface
{
    public function supports(OperationInterface $operation, Request $request): bool
    {
        return $operation instanceof CollectionOperationInterface && $request->query->has('page');
    }

    /** @param CollectionOperationInterface $operation */
    public function buildContext(OperationInterface $operation, Request $request): array
    {
        return ['page' => $request->query->getInt($operation->getPageParameter(), 1)];
    }
}

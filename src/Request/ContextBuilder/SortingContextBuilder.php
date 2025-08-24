<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Request\ContextBuilder;

use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\OperationInterface;
use Symfony\Component\HttpFoundation\Request;

final readonly class SortingContextBuilder implements ContextBuilderInterface
{
    public function supports(OperationInterface $operation, Request $request): bool
    {
        return $operation instanceof CollectionOperationInterface;
    }

    /** @param CollectionOperationInterface $operation */
    public function buildContext(OperationInterface $operation, Request $request): array
    {
        $context = [];

        if ($request->query->has('sort')) {
            $context['sort'] = $request->query->get('sort');
        }

        if ($request->query->has('order')) {
            $context['order'] = $request->query->get('order');
        }

        return $context;
    }
}

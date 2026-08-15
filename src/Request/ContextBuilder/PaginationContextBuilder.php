<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Request\ContextBuilder;

use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\GridInterface;
use LAG\AdminBundle\Metadata\OperationInterface;
use Symfony\Component\HttpFoundation\Request;

final readonly class PaginationContextBuilder implements ContextBuilderInterface
{
    public function __construct(
        private ContextBuilderInterface $contextBuilder,
    ) {
    }

    public function buildContext(Request $request, OperationInterface $operation, ?GridInterface $grid = null): array
    {
        $context = $this->contextBuilder->buildContext($request, $operation, $grid);

        if ($operation instanceof CollectionOperationInterface) {
            $context['page'] = $request->query->getInt($operation->getPageParameter(), 1);
        }

        return $context;
    }
}

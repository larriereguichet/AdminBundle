<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Request\ContextBuilder;

use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\GridInterface;
use LAG\AdminBundle\Metadata\OperationInterface;
use Symfony\Component\HttpFoundation\Request;

final readonly class SortingContextBuilder implements ContextBuilderInterface
{
    public function __construct(
        private ContextBuilderInterface $contextBuilder,
    ) {
    }

    public function buildContext(Request $request, OperationInterface $operation, ?GridInterface $grid = null): array
    {
        $context = $this->contextBuilder->buildContext($request, $operation, $grid);

        if (!$operation instanceof CollectionOperationInterface || $grid === null) {
            return $context;
        }
        $context['sort'] = [];

        $sortParameter = $grid->getSortParameter();
        $orderParameter = $grid->getOrderParameter();

        if ($request->query->has($sortParameter)) {
            $context['sort'] = $request->query->get($sortParameter);
        }

        if ($request->query->has($orderParameter)) {
            $context['order'] = $request->query->get($sortParameter);
        }

        return $context;
    }
}

<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Request\Context;

use LAG\AdminBundle\Resource\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use Symfony\Component\HttpFoundation\Request;

final readonly class SortingContextProvider implements ContextProviderInterface
{
    public function __construct(
        private ContextProviderInterface $contextProvider
    ) {
    }

    public function getContext(OperationInterface $operation, Request $request): array
    {
        $context = $this->contextProvider->getContext($operation, $request);

        if (!$operation instanceof CollectionOperationInterface) {
            return $context;
        }
        $pageParameter = $operation->getPageParameter();

        if ($request->query->has($pageParameter)) {
            $context[$pageParameter] = $request->query->get($pageParameter);
        }

        if ($request->query->has('sort')) {
            $context['sort'] = $request->query->get('sort');
        }

        if ($request->query->has('order')) {
            $context['order'] = strtoupper($request->query->get('order'));
        }

        return $context;
    }
}

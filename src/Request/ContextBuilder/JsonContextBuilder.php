<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Request\ContextBuilder;

use LAG\AdminBundle\Metadata\GridInterface;
use LAG\AdminBundle\Metadata\OperationInterface;
use Symfony\Component\HttpFoundation\Request;

final readonly class JsonContextBuilder implements ContextBuilderInterface
{
    public function __construct(
        private ContextBuilderInterface $contextBuilder,
    ) {
    }

    public function buildContext(Request $request, OperationInterface $operation, ?GridInterface $grid = null): array
    {
        $context = $this->contextBuilder->buildContext($request, $operation, $grid);

        if ($request->getContentTypeFormat() === 'json') {
            $context['json'] = true;
        }

        return $context;
    }
}

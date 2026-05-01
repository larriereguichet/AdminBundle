<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Request\ContextBuilder;

use LAG\AdminBundle\Metadata\GridInterface;
use LAG\AdminBundle\Metadata\OperationInterface;
use Symfony\Component\HttpFoundation\Request;

final readonly class PartialContextBuilder implements ContextBuilderInterface
{
    public const string EMBEDDED_REQUEST_ATTRIBUTE = '_lag_admin_embedded';

    public function __construct(
        private ContextBuilderInterface $contextBuilder,
    ) {
    }

    public function buildContext(Request $request, OperationInterface $operation, ?GridInterface $grid = null): array
    {
        $context = $this->contextBuilder->buildContext($request, $operation, $grid);
        $context['partial'] = $request->attributes->getBoolean(self::EMBEDDED_REQUEST_ATTRIBUTE);

        return $context;
    }
}

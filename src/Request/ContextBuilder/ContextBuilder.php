<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Request\ContextBuilder;

use LAG\AdminBundle\Metadata\GridInterface;
use LAG\AdminBundle\Metadata\OperationInterface;
use Symfony\Component\HttpFoundation\Request;

final readonly class ContextBuilder implements ContextBuilderInterface
{
    public function supports(Request $request, OperationInterface $operation, ?GridInterface $grid): bool
    {
        return true;
    }

    public function buildContext(Request $request, OperationInterface $operation, ?GridInterface $grid): array
    {
        return $operation->getContext();
    }
}

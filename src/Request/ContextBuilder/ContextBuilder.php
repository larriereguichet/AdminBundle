<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Request\ContextBuilder;

use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use Symfony\Component\HttpFoundation\Request;

final readonly class ContextBuilder implements ContextBuilderInterface
{
    public function buildContext(OperationInterface $operation, Request $request): array
    {
        return $operation->getContext();
    }
}

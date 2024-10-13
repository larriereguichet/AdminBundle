<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Request\Context;

use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use Symfony\Component\HttpFoundation\Request;

final readonly class OperationContextProvider implements ContextProviderInterface
{
    public function getContext(OperationInterface $operation, Request $request): array
    {
        return $operation->getContext();
    }
}

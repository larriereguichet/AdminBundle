<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Request\Context;

use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use Symfony\Component\HttpFoundation\Request;

interface ContextProviderInterface
{
    /** @return array<string, mixed> */
    public function getContext(OperationInterface $operation, Request $request): array;
}

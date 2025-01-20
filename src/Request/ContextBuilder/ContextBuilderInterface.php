<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Request\ContextBuilder;

use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use Symfony\Component\HttpFoundation\Request;

interface ContextBuilderInterface
{
    /** @return array<string, mixed> */
    public function buildContext(OperationInterface $operation, Request $request): array;
}

<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Request\ContextBuilder;

use LAG\AdminBundle\Metadata\GridInterface;
use LAG\AdminBundle\Metadata\OperationInterface;
use Symfony\Component\HttpFoundation\Request;

interface ContextBuilderInterface
{
    /** @return array<string, mixed> */
    public function buildContext(Request $request, OperationInterface $operation, ?GridInterface $grid = null): array;
}

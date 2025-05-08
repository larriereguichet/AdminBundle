<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Request\ContextBuilder;

use LAG\AdminBundle\Metadata\OperationInterface;
use Symfony\Component\HttpFoundation\Request;

interface ContextBuilderInterface
{
    public const string SERVICE_TAG = 'lag_admin.request.context_builder';

    public function supports(OperationInterface $operation, Request $request): bool;

    /** @return array<string, mixed> */
    public function buildContext(OperationInterface $operation, Request $request): array;
}

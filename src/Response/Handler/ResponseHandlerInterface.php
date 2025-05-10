<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Response\Handler;

use LAG\AdminBundle\Metadata\OperationInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Handle response creation.
 */
interface ResponseHandlerInterface
{
    public function createResponse(OperationInterface $operation, mixed $data, Request $request, array $context = []): Response;
}

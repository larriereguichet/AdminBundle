<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Response\Handler;

use LAG\AdminBundle\Metadata\OperationInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface ContentResponseHandlerInterface
{
    /**
     * Create an HTTP response according to the given operation and context. It could be a Twig template or a json
     * response for instance.
     *
     * @param array<string, mixed> $context
     */
    public function createResponse(
        Request $request,
        OperationInterface $operation,
        mixed $data,
        array $context = [],
    ): Response;
}

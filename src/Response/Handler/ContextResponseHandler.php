<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Response\Handler;

use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Request\ContextBuilder\ContextBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class ContextResponseHandler implements ResponseHandlerInterface
{
    public function __construct(
        private ResponseHandlerInterface $responseHandler,
        private ContextBuilderInterface $contextBuilder,
    ) {
    }

    public function createResponse(OperationInterface $operation, mixed $data, Request $request, array $context = []): Response
    {
        // TODO use a response context builder
        $context += $this->contextBuilder->buildContext($operation, $request);

        return $this->responseHandler->createResponse($operation, $data, $request, $context);
    }
}

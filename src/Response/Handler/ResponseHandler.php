<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Response\Handler;

use LAG\AdminBundle\Metadata\OperationInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

final readonly class ResponseHandler implements ResponseHandlerInterface
{
    public function __construct(
        private ContentResponseHandlerInterface $responseHandler,
        private RedirectResponseHandlerInterface $redirectHandler,
    ) {
    }

    public function createResponse(OperationInterface $operation, mixed $data, array $context = []): Response
    {
        return $this->responseHandler->createResponse($operation, $data, $context);
    }

    public function createRedirectResponse(OperationInterface $operation, mixed $data, array $context = []): RedirectResponse
    {
        return $this->redirectHandler->createRedirectResponse($operation, $data, $context);
    }
}

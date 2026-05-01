<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Response\Handler;

use LAG\AdminBundle\Metadata\OperationInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class ResponseHandler implements ResponseHandlerInterface
{
    public function __construct(
        private ContentResponseHandlerInterface $responseHandler,
        private RedirectResponseHandlerInterface $redirectResponseHandler,
    ) {
    }

    /** @param array<string, mixed> $context */
    public function createResponse(Request $request, OperationInterface $operation, mixed $data, array $context = []): Response
    {
        return $this->responseHandler->createResponse($request, $operation, $data, $context);
    }

    /** @param array<string, mixed> $context */
    public function createRedirectResponse(Request $request, OperationInterface $operation, mixed $data, array $context = []): RedirectResponse
    {
        return $this->redirectResponseHandler->createRedirectResponse($request, $operation, $data, $context);
    }
}

<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Response\Handler;

use LAG\AdminBundle\Exception\UnhandledResponseException;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class CompositeResponseHandler implements ResponseHandlerInterface
{
    public function __construct(
        /** @var iterable<ResponseHandlerInterface> $responseHandlers */
        private iterable $responseHandlers,
    ) {
    }

    public function supports(OperationInterface $operation, mixed $data, Request $request, array $context = []): bool
    {
        dd(iterator_to_array($this->responseHandlers));
        foreach ($this->responseHandlers as $responseHandler) {
            if ($responseHandler->supports($operation, $data, $request, $context)) {
                return true;
            }
        }

        return false;
    }

    public function createResponse(OperationInterface $operation, mixed $data, Request $request, array $context = []): Response
    {
        foreach ($this->responseHandlers as $responseHandler) {
            if ($responseHandler->supports($operation, $data, $request, $context)) {
                return $responseHandler->createResponse($operation, $data, $request, $context);
            }
        }

        throw new UnhandledResponseException();
    }
}
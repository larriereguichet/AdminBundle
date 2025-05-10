<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Response\Handler;

use LAG\AdminBundle\Metadata\OperationInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

final readonly class JsonResponseHandler implements ResponseHandlerInterface
{
    public function __construct(
        private ResponseHandlerInterface $responseHandler,
        private SerializerInterface $serializer,
    ) {
    }

    public function createResponse(OperationInterface $operation, mixed $data, Request $request, array $context = []): Response
    {
        if ($request->getContentTypeFormat() !== 'json') {
            return $this->responseHandler->createResponse($operation, $data, $request, $context);
        }
        $content = $this->serializer->serialize($data, 'json', $operation->getNormalizationContext());

        return new JsonResponse($content, Response::HTTP_OK, [], true);
    }
}

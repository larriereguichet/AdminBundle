<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Response\Handler;

use LAG\AdminBundle\Metadata\OperationInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

final readonly class JsonResponseHandler implements ContentResponseHandlerInterface
{
    public function __construct(
        private ContentResponseHandlerInterface $responseHandler,
        private SerializerInterface $serializer,
    ) {
    }

    /** @param array<string, mixed> $context */
    public function createResponse(
        Request $request,
        OperationInterface $operation,
        mixed $data,
        array $context = [],
    ): Response {

        if ($request->getContentTypeFormat() !== 'json') {
            return $this->responseHandler->createResponse($request, $operation, $data, $context);
        }
        $content = $this->serializer->serialize($data, 'json', $operation->getNormalizationContext());

        return new JsonResponse($content, Response::HTTP_OK, [], true);
    }
}

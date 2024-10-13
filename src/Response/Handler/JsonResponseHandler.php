<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Response\Handler;

use LAG\AdminBundle\Grid\View\GridView;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use Symfony\Component\Form\FormInterface;
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

    public function createResponse(Request $request, OperationInterface $operation, mixed $data, ?FormInterface $form = null, ?FormInterface $filterForm = null, ?GridView $grid = null): Response
    {
        if ($request->getContentTypeFormat() === 'json') {
            $content = $this->serializer->serialize($data, 'json', $operation->getNormalizationContext());

            return new JsonResponse($content, Response::HTTP_OK, [], true);
        }

        return $this->responseHandler->createResponse($request, $operation, $data, $form, $filterForm, $grid);
    }

    public function createRedirectResponse(OperationInterface $operation, mixed $data, array $context = []): Response
    {
        return $this->responseHandler->createRedirectResponse($operation, $data, $context);
    }
}

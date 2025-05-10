<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Response\Handler;

use LAG\AdminBundle\Metadata\OperationInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class FormResponseHandler implements ResponseHandlerInterface
{
    public function __construct(
        private ResponseHandlerInterface $responseHandler,
    ) {
    }

    public function createResponse(OperationInterface $operation, mixed $data, Request $request, array $context = []): Response
    {
        foreach ($context as $name => $form) {
            if (!$form instanceof FormInterface) {
                continue;
            }
            $context[$name] = $form->createView();

            if ($form->isSubmitted() && !$form->isValid()) {
                $context['responseCode'] = Response::HTTP_UNPROCESSABLE_ENTITY;
            }
        }

        return $this->responseHandler->createResponse($operation, $data, $request, $context);
    }
}

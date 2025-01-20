<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Controller\Resource;

use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use LAG\AdminBundle\Response\Handler\ResponseHandlerInterface;
use LAG\AdminBundle\State\Provider\ProviderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class HandleLiveForm
{
    public function __construct(
        private ProviderInterface $provider,
        private FormFactoryInterface $formFactory,
        private ResponseHandlerInterface $responseHandler,
    ) {
    }

    public function __invoke(OperationInterface $operation, Request $request): Response
    {
        $data = $this->provider->provide($operation);
        $form = $this->formFactory->create($operation->getForm(), $data, $operation->getFormOptions());
        $form->handleRequest($request);

        return $this->responseHandler->createResponse($operation, $data, $form);
    }
}
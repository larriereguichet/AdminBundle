<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Controller\Resource;

use LAG\AdminBundle\Event\ResourceControllerEvent;
use LAG\AdminBundle\Event\ResourceControllerEvents;
use LAG\AdminBundle\EventDispatcher\ResourceEventDispatcherInterface;
use LAG\AdminBundle\Request\Context\ContextProviderInterface;
use LAG\AdminBundle\Request\Uri\UriVariablesExtractorInterface;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use LAG\AdminBundle\Response\Handler\ResponseHandlerInterface;
use LAG\AdminBundle\State\Processor\ProcessorInterface;
use LAG\AdminBundle\State\Provider\ProviderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class ResourceController
{
    public function __construct(
        private UriVariablesExtractorInterface $uriVariablesExtractor,
        private ContextProviderInterface $contextProvider,
        private ProviderInterface $provider,
        private ProcessorInterface $processor,
        private FormFactoryInterface $formFactory,
        private ResourceEventDispatcherInterface $eventDispatcher,
        private ResponseHandlerInterface $responseHandler,
    ) {
    }

    public function __invoke(Request $request, OperationInterface $operation): Response
    {
        $uriVariables = $this->uriVariablesExtractor->extractVariables($operation, $request);
        $context = $this->contextProvider->getContext($operation, $request);
        $data = $this->provider->provide($operation, $uriVariables, $context);
        $form = null;

        if ($data === null) {
            throw new NotFoundHttpException();
        }

        if ($operation->getForm() !== null) {
            $form = $this->formFactory->create($operation->getForm(), $data, $operation->getFormOptions());
            $form->handleRequest($request);

            if ($context['json'] ?? false) {
                $form->submit($request->toArray());
            }

            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();
                $this->processor->process($data, $operation, $uriVariables, $context);

                return $this->responseHandler->createRedirectResponse($operation, $data, $context);
            }
        }
        $this->eventDispatcher->dispatchEvents(
            $event = new ResourceControllerEvent($operation, $request, $data, $context),
            ResourceControllerEvents::RESOURCE_CONTROLLER_PATTERN,
            $operation->getResource()->getApplication(),
            $operation->getResource()->getName(),
            $operation->getName(),
        );

        if ($event->getResponse() !== null) {
            return $event->getResponse();
        }

        return $this->responseHandler->createResponse(
            request: $request,
            operation: $operation,
            data: $data,
            form: $form,
        );
    }
}

<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Controller\Resource;

use LAG\AdminBundle\Event\ResourceControllerEvent;
use LAG\AdminBundle\Event\ResourceControllerEvents;
use LAG\AdminBundle\EventDispatcher\ResourceEventDispatcherInterface;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Request\ContextBuilder\ContextBuilderInterface;
use LAG\AdminBundle\Response\Handler\ResponseHandlerInterface;
use LAG\AdminBundle\State\Processor\ProcessorInterface;
use LAG\AdminBundle\State\Provider\ProviderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class ProcessResource
{
    public function __construct(
        private ContextBuilderInterface $contextBuilder,
        private ProviderInterface $provider,
        private ProcessorInterface $processor,
        private FormFactoryInterface $formFactory,
        private ResourceEventDispatcherInterface $eventDispatcher,
        private ResponseHandlerInterface $responseHandler,
    ) {
    }

    public function __invoke(OperationInterface $operation, Request $request): Response
    {
        $context = $this->contextBuilder->buildContext($operation, $request);
        $data = $this->provider->provide($operation, [], $context);
        $form = $this->formFactory->create($operation->getForm(), $data, $operation->getFormOptions());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $this->processor->process($data, $operation, [], $context);

            return $this->responseHandler->createRedirectResponse($operation, $data, ['form' => $form]);
        }
        $event = new ResourceControllerEvent($operation, $request, $data);
        $this->eventDispatcher->dispatchEvents($event, ResourceControllerEvents::RESOURCE_CONTROLLER);

        if ($event->getResponse() !== null) {
            return $event->getResponse();
        }

        return $this->responseHandler->createResponse($operation, $data, ['form' => $form]);
    }
}

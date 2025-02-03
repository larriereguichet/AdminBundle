<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Controller\Resource;

use LAG\AdminBundle\Event\ResourceControllerEvent;
use LAG\AdminBundle\Event\ResourceControllerEvents;
use LAG\AdminBundle\EventDispatcher\ResourceEventDispatcherInterface;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use LAG\AdminBundle\Response\Handler\ResponseHandlerInterface;
use LAG\AdminBundle\State\Processor\ProcessorInterface;
use LAG\AdminBundle\State\Provider\ProviderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class ProcessResource
{
    public function __construct(
        private ProviderInterface $provider,
        private ProcessorInterface $processor,
        private FormFactoryInterface $formFactory,
        private ResourceEventDispatcherInterface $eventDispatcher,
        private ResponseHandlerInterface $responseHandler,
    ) {
    }

    public function __invoke(OperationInterface $operation, Request $request): Response
    {
        $data = $this->provider->provide($operation);
        $form = $this->formFactory->create($operation->getForm(), $data, $operation->getFormOptions());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $this->processor->process($data, $operation);

            return $this->responseHandler->createResponse($operation, $data, $request, [
                'form' => $form->createView(),
                'submitted' => true,
            ]);
        }
        $event = new ResourceControllerEvent($operation, $request, $data);

        $this->eventDispatcher->dispatchOperationEvents(
            $event,
            ResourceControllerEvents::RESOURCE_CONTROLLER_PATTERN,
            $operation,
        );

        if ($event->getResponse() !== null) {
            return $event->getResponse();
        }

        return $this->responseHandler->createResponse($operation, $data, $request, [
            'form' => $form->createView(),
            'submitted' => $form->isSubmitted(),
        ]);
    }
}
<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Controller\Resource;

use LAG\AdminBundle\Event\ResourceControllerEvent;
use LAG\AdminBundle\Event\ResourceControllerEvents;
use LAG\AdminBundle\EventDispatcher\ResourceEventDispatcherInterface;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Request\ContextBuilder\ContextBuilderInterface;
use LAG\AdminBundle\Response\Handler\ResponseHandlerInterface;
use LAG\AdminBundle\State\Provider\ProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class ShowResource
{
    public function __construct(
        private ContextBuilderInterface $contextBuilder,
        private ProviderInterface $provider,
        private ResourceEventDispatcherInterface $eventDispatcher,
        private ResponseHandlerInterface $responseHandler,
    ) {
    }

    public function __invoke(OperationInterface $operation, Request $request): Response
    {
        $context = $this->contextBuilder->buildContext($operation, $request);
        $data = $this->provider->provide($operation, [], $context);

        $this->eventDispatcher->dispatchEvents(
            $event = new ResourceControllerEvent($operation, $request, $data),
            ResourceControllerEvents::RESOURCE_CONTROLLER,
        );

        if ($event->getResponse() !== null) {
            return $event->getResponse();
        }

        return $this->responseHandler->createResponse($operation, $data);
    }
}

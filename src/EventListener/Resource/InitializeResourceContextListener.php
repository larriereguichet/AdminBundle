<?php

declare(strict_types=1);

namespace LAG\AdminBundle\EventListener\Resource;

use LAG\AdminBundle\Resource\Context\ResourceContextInterface;
use LAG\AdminBundle\Resource\Factory\ResourceFactoryInterface;
use Symfony\Component\HttpKernel\Event\FinishRequestEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;

use function Symfony\Component\String\u;

final readonly class InitializeResourceContextListener
{
    /**
     * Marks the requests this listener pushed a frame for, so a request short-circuited by an earlier listener
     * (a firewall redirect, an HTTP cache hit) does not pop the frame of its parent request.
     */
    private const string CONTEXT_PUSHED_ATTRIBUTE = '_lag_resource_context_pushed';

    public function __construct(
        private string $requestParameter,
        private ResourceContextInterface $resourceContext,
        private ResourceFactoryInterface $resourceFactory,
    ) {
    }

    public function onRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        $this->resourceContext->push();
        $request->attributes->set(self::CONTEXT_PUSHED_ATTRIBUTE, true);

        if (!$request->attributes->has($this->requestParameter)) {
            return;
        }
        $requestParameter = u($request->attributes->get($this->requestParameter));
        $resourceName = (string) $requestParameter->beforeLast('.');
        $operationName = (string) $requestParameter->afterLast('.');

        if ($resourceName === '' || $operationName === '') {
            return;
        }
        $resource = $this->resourceFactory->create($resourceName);

        $this->resourceContext->setResource($resource);
        $this->resourceContext->setOperation($resource->getOperation($operationName));
    }

    public function onFinishRequest(FinishRequestEvent $event): void
    {
        $request = $event->getRequest();

        if (!$request->attributes->get(self::CONTEXT_PUSHED_ATTRIBUTE, false)) {
            return;
        }
        $request->attributes->remove(self::CONTEXT_PUSHED_ATTRIBUTE);
        $this->resourceContext->pop();
    }
}

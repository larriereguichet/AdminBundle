<?php

declare(strict_types=1);

namespace LAG\AdminBundle\EventListener\Resource;

use LAG\AdminBundle\Resource\Context\ResourceContextInterface;
use LAG\AdminBundle\Resource\Factory\ResourceFactoryInterface;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use function Symfony\Component\String\u;

final readonly class DefineResourceContextListener
{
    public function __construct(
        private string $requestParameter,
        private ResourceContextInterface $resourceContext,
        private ResourceFactoryInterface $resourceFactory,
    ) {
    }

    public function __invoke(KernelEvent $event): void
    {
        $request = $event->getRequest();

        if (!$request->attributes->has($this->requestParameter) || $this->resourceContext->hasOperation()) {
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
}

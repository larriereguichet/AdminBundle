<?php

declare(strict_types=1);

namespace LAG\AdminBundle\EventListener\Resource;

use LAG\AdminBundle\Event\ResourceEvent;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\Link;
use LAG\AdminBundle\Routing\Route\RouteNameGeneratorInterface;

readonly class InitializeResourceOperationsListener
{
    public function __construct(
        private RouteNameGeneratorInterface $routeNameGenerator,
    ) {
    }

    public function __invoke(ResourceEvent $event): void
    {
        $resource = $event->getResource();
        $operations = [];

        foreach ($resource->getOperations() as $operation) {
            if ($operation instanceof CollectionOperationInterface) {
                if ($operation->getContextualActions() === null && $resource->hasOperation('create')) {
                    $operation = $operation->withContextualActions([new Link(
                        resourceName: $resource->getName(),
                        operationName: 'create',
                        label: $resource->getOperation('create')->getTitle() ?? 'Create',
                        type: 'primary',
                    )]);
                }

                if ($operation->getItemActions() === null) {
                    $operation = $operation->withItemActions([]);
                }
            }

            if (!$operation->getRedirectRoute()) {
                if ($resource->hasOperation('get_collection')) {
                    $operation = $operation->withRedirectRoute(
                        $this->routeNameGenerator->generateRouteName($resource, $resource->getOperation('get_collection')),
                    );
                } elseif ($resource->hasOperation('update')) {
                    $operation = $operation->withRedirectRoute(
                        $this->routeNameGenerator->generateRouteName($resource, $resource->getOperation('update')),
                    );
                }
            }

            if ($operation->getContextualActions() === null) {
                $operation = $operation->withContextualActions([]);
            }
            $operations[] = $operation;
        }
        $event->setResource($resource->withOperations($operations));
    }
}

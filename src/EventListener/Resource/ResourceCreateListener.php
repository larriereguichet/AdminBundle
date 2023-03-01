<?php

declare(strict_types=1);

namespace LAG\AdminBundle\EventListener\Resource;

use LAG\AdminBundle\Event\Events\ResourceEvent;
use LAG\AdminBundle\Metadata\AdminResource;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\Create;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Routing\Route\RouteNameGeneratorInterface;
use Symfony\Component\String\Inflector\EnglishInflector;

use function Symfony\Component\String\u;

class ResourceCreateListener
{
    public function __construct(
        private RouteNameGeneratorInterface $routeNameGenerator,
    ) {
    }

    public function __invoke(ResourceEvent $event): void
    {
        $resource = $event->getResource();
        $operations = [];
        $resource = $this->addResourceDefault($resource);

        foreach ($resource->getOperations() as $operation) {
            $operation = $this->addOperationDefault($resource, $operation)->withResource($resource);
            $operations[$operation->getName()] = $operation;
        }
        $resource = $resource->withOperations($operations);
        $event->setResource($resource);
    }

    private function addResourceDefault(AdminResource $resource): AdminResource
    {
        if (!$resource->getTitle()) {
            $inflector = new EnglishInflector();
            $resource = $resource->withTitle($inflector->pluralize($resource->getName())[0]);
        }

        return $resource;
    }

    private function addOperationDefault(AdminResource $resource, OperationInterface $operation): OperationInterface
    {
        if (!$operation->getName()) {
            $operation = $operation->withName(
                u(\get_class($operation))
                    ->afterLast('\\')
                    ->snake()
                    ->lower()
                    ->toString()
            );
        }

        if (!$operation->getTitle()) {
            $operation = $operation->withTitle(match ($operation->getName()) {
                'index' => 'List',
                default => u($operation->getName())->title(true)->toString(),
            });
        }

        if (!$operation->getRoute()) {
            $route = $this->routeNameGenerator->generateRouteName($resource, $operation);
            $operation = $operation->withRoute($route);
        }

        if (!$operation->getRouteParameters()) {
            if (!$operation instanceof CollectionOperationInterface && !$operation instanceof Create) {
                $operation = $operation->withRouteParameters(array_keys($operation->getIdentifiers()));
            } else {
                $operation = $operation->withRouteParameters([]);
            }
        }

        return $operation;
    }
}

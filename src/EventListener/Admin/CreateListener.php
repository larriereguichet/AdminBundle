<?php

declare(strict_types=1);

namespace LAG\AdminBundle\EventListener\Admin;

use LAG\AdminBundle\Event\Events\ResourceCreateEvent;
use LAG\AdminBundle\Metadata\AdminResource;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\Create;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Routing\Route\RouteNameGeneratorInterface;
use Symfony\Component\String\Inflector\EnglishInflector;

use function Symfony\Component\String\u;

class CreateListener
{
    public function __construct(
        private RouteNameGeneratorInterface $routeNameGenerator,
    ) {
    }

    public function __invoke(ResourceCreateEvent $event): void
    {
        $resource = $event->getResource();
        $operations = [];
        $resource = $this->addResourceDefault($resource);

        foreach ($resource->getOperations() as $operation) {
            $operation = $this->addOperationDefault($resource, $operation)->withResource($resource);
            $operations[] = $operation;
        }
        $resource = $resource->withOperations($operations);
        $event->setResource($resource);
    }

    private function addResourceDefault(AdminResource $resource): AdminResource
    {
        if (!$resource->getTitle()) {
            $title = u($resource->getName())->camel()->title()->toString();
            $resource = $resource->withTitle($title);
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

        if (!$operation->getResourceName()) {
            $operation = $operation->withResourceName($resource->getName());
        }

        if (!$operation->getTitle()) {
            if ($operation instanceof CollectionOperationInterface) {
                $inflector = new EnglishInflector();
                $title = u($inflector->pluralize($resource->getTitle())[0])
                    ->title(true)
                    ->toString()
                ;
            } else {
                $title = u($resource->getTitle())
                    ->append(' ')
                    ->append($operation->getName())
                    ->title(true)
                    ->toString()
                ;
            }
            $operation = $operation->withTitle($title);
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

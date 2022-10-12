<?php

namespace LAG\AdminBundle\EventListener\Admin;

use LAG\AdminBundle\Event\AdminEvent;
use LAG\AdminBundle\Form\Type\OperationDataType;
use LAG\AdminBundle\Metadata\Action;
use LAG\AdminBundle\Metadata\AdminResource;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\Create;
use LAG\AdminBundle\Metadata\Delete;
use LAG\AdminBundle\Metadata\Index;
use LAG\AdminBundle\Metadata\Operation;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Metadata\Show;
use LAG\AdminBundle\Metadata\Update;
use LAG\AdminBundle\Routing\Route\RouteNameGeneratorInterface;
use Symfony\Component\String\Inflector\EnglishInflector;
use function Symfony\Component\String\u;

class CreateListener
{
    public function __construct(
        private RouteNameGeneratorInterface $routeNameGenerator,
    ) {
    }

    public function __invoke(AdminEvent $event): void
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

        if ($resource->getPrefix() === null) {
            $resource = $resource->withPrefix($resource->getName());
        }

        return $resource;
    }

    private function addOperationDefault(AdminResource $resource, OperationInterface $operation): Operation
    {
        if (!$operation->getName()) {
            $operation = $operation->withName(
                u(get_class($operation))
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
                // TODO identifiers
                $operation = $operation->withRouteParameters([
                    'id' => null,
                ]);
            } else {
                $operation = $operation->withRouteParameters([]);
            }
        }

        return $operation;
    }
}

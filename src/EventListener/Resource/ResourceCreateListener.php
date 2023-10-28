<?php

declare(strict_types=1);

namespace LAG\AdminBundle\EventListener\Resource;

use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
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
        private ApplicationConfiguration $applicationConfiguration,
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

        if (!$resource->getApplicationName()) {
            $resource = $resource->withApplicationName($this->applicationConfiguration->getString('name'));
        }

        if (!$resource->getTranslationDomain()) {
            $resource = $resource->withTranslationDomain($this->applicationConfiguration->getString('translation_domain'));
        }

        return $resource;
    }

    private function addOperationDefault(AdminResource $resource, OperationInterface $operation): OperationInterface
    {
        if (!$operation->getName()) {
            $operation = $operation->withName(
                u($operation::class)
                    ->afterLast('\\')
                    ->snake()
                    ->lower()
                    ->toString()
            );
        }

        if (!$operation->getTitle()) {
            $inflector = new EnglishInflector();

            if ($operation instanceof CollectionOperationInterface) {
                $title = u($inflector->pluralize($resource->getName())[0]);
            } else {
                $title = u($operation->getName())->append(' ')->append($resource->getName());
            }
            $operation = $operation->withTitle($title->title(true)->toString());
        }

        if (!$operation->getRoute()) {
            $route = $this->routeNameGenerator->generateRouteName($resource, $operation);
            $operation = $operation->withRoute($route);
        }

        if (!$operation->getRouteParameters()) {
            $operation = $operation->withRouteParameters([]);

            if (!$operation instanceof CollectionOperationInterface && !$operation instanceof Create) {
                $routeParameters = [];

                foreach ($operation->getIdentifiers() as $identifier) {
                    $routeParameters[$identifier] = null;
                }
                $operation = $operation->withRouteParameters($routeParameters);
            }
        }

        return $operation;
    }
}

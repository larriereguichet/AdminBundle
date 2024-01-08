<?php

namespace LAG\AdminBundle\EventListener\Operation;

use LAG\AdminBundle\Event\OperationEvent;
use LAG\AdminBundle\Form\Type\Resource\FilterType;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\GetCollection;

class InitializeCollectionOperationListener
{
    public function __invoke(OperationEvent $event): void
    {
        $operation = $event->getOperation();
        $resource = $operation->getResource();

        if (!$operation instanceof CollectionOperationInterface) {
            return;
        }

        if ($operation->getFilters() === null) {
            $operation = $operation->withFilters([]);
        }

        if ($operation->getFilterFormType() === null && $operation instanceof GetCollection && \count($operation->getFilters() ?? []) > 0) {
            $operation = $operation->withFilterFormType(FilterType::class);
        }

        if (is_a($operation->getFilterFormType(), FilterType::class, true)) {
            $operation = $operation->withFilterFormOptions(array_merge([
                'application' => $resource->getApplicationName(),
                'resource' => $resource->getName(),
                'operation' => $operation->getName(),
            ], $operation->getFilterFormOptions()));
        }

        $event->setOperation($operation);
    }
}

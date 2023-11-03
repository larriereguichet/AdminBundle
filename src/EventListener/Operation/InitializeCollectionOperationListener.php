<?php

namespace LAG\AdminBundle\EventListener\Operation;

use LAG\AdminBundle\Event\Events\OperationEvent;
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

        if ($operation->getFilterFormType() === null && $operation instanceof GetCollection && \count($operation->getFilters() ?? []) > 0) {
            $operation = $operation->withFilterFormType(FilterType::class);
        }

        if (is_a($operation->getFilterFormType(), FilterType::class, true)) {
            $operation = $operation->withFilterFormOptions($operation->getFilterFormOptions() ?? [])
                ->withFilterFormOptions([
                'resource' => $resource->getName(),
                'operation' => $operation->getName(),
            ]);
        }

        $event->setOperation($operation);
    }
}

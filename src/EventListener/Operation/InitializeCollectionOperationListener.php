<?php

namespace LAG\AdminBundle\EventListener\Operation;

use LAG\AdminBundle\Event\OperationEvent;
use LAG\AdminBundle\Form\Type\Resource\FilterType;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\Index;

readonly class InitializeCollectionOperationListener
{
    public function __invoke(OperationEvent $event): void
    {
        $operation = $event->getOperation();
        $resource = $operation->getResource();

        if (!$operation instanceof CollectionOperationInterface) {
            return;
        }

        // TODO refactor filters
        if ($operation->getFilters() === null) {
            $operation = $operation->withFilters([]);
        }

        if ($operation->getFilterFormType() === null && $operation instanceof Index && \count($operation->getFilters() ?? []) > 0) {
            $operation = $operation->withFilterFormType(FilterType::class);
        }

        if (is_a($operation->getFilterFormType(), FilterType::class, true)) {
            $operation = $operation->withFilterFormOptions(array_merge([
                'application' => $resource->getApplication(),
                'resource' => $resource->getName(),
                'operation' => $operation->getName(),
            ], $operation->getFilterFormOptions()));
        }

        if ($operation->getItemFormOptions() === null) {
            $operation = $operation->withItemFormOptions([]);
        }

        if ($operation->getCollectionFormOptions() === null) {
            $operation = $operation->withCollectionFormOptions([]);
        }

        $event->setOperation($operation);
    }
}

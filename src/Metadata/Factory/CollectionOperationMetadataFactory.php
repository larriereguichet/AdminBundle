<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Factory;

use LAG\AdminBundle\Form\Type\Resource\FilterType;
use LAG\AdminBundle\Metadata\Attribute\EntityFilter;
use LAG\AdminBundle\Metadata\CollectionOperationMetadataInterface;
use LAG\AdminBundle\Metadata\OperationMetadataInterface;

final readonly class CollectionOperationMetadataFactory implements OperationMetadataFactoryInterface
{
    public function __construct(
        private OperationMetadataFactoryInterface $metadataFactory,
    ) {
    }

    public function createMetadata(OperationMetadataInterface $operation): OperationMetadataInterface
    {
        $operation = $this->metadataFactory->createMetadata($operation);

        if (!$operation instanceof CollectionOperationMetadataInterface) {
            return $operation;
        }

        if ($operation->getFilters() === null) {
            $operation = $operation->withFilters([]);
        }

        if ($operation->getFilterForm() === null && \count($operation->getFilters() ?? []) > 0) {
            $operation = $operation
                ->withFilterForm(FilterType::class)
                ->withFilterFormOptions(['operation' => $operation->getName()])
            ;
        }

        if ($operation->getCollectionFormOptions() === null) {
            $operation = $operation->withCollectionFormOptions([]);
        }

        if ($operation->getCollectionActions() === null) {
            $collectionActions = [];

            $operation = $operation->withCollectionActions($collectionActions);
        }
        $filters = $operation->getFilters();

        foreach ($filters as $index => $filter) {
            $formOptions = $filter->getFormOptions();

            if ($filter instanceof EntityFilter) {
                if (empty($formOptions['multiple']) && $filter->isMultiple()) {
                    $formOptions['multiple'] = true;
                }

                if ($filter->getProperty() === null) {
                    $filter = $filter->withProperty($filter->getName());
                }
            }
            $filters[$index] = $filter->withFormOptions($formOptions);
        }

        return $operation->withFilters($filters);
    }
}

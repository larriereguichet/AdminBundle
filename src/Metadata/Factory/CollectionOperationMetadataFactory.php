<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Factory;

use LAG\AdminBundle\Form\Type\Resource\FilterType;
use LAG\AdminBundle\Metadata\Attribute\EntityFilter;
use LAG\AdminBundle\Metadata\CollectionOperationMetadataInterface;
use LAG\AdminBundle\Metadata\ResourceMetadataInterface;

final readonly class CollectionOperationMetadataFactory implements ResourceMetadataFactoryInterface
{
    public function __construct(
        private ResourceMetadataFactoryInterface $metadataFactory,
    ) {
    }

    public function createMetadata(string $resourceName): ResourceMetadataInterface
    {
        $resource = $this->metadataFactory->createMetadata($resourceName);
        $operations = [];

        foreach ($resource->getOperations() as $operation) {
            if (!$operation instanceof CollectionOperationMetadataInterface) {
                $operations[$operation->getName()] = $operation;

                continue;
            }
            $filters = $operation->getFilters() ?? [];
            $filterForm = $operation->getFilterForm();
            $filterFormOptions = $operation->getFilterFormOptions();

            if ($filterForm === null) {
                $filterForm = FilterType::class;
                $filterFormOptions = ['filters' => $filters];
            }

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

            $operations[$operation->getName()] = $operation
                ->withCollectionLinks($operation->getCollectionLinks() ?? [])
                ->withFilters($filters)
                ->withFilterForm($filterForm)
                ->withFilterFormOptions($filterFormOptions)
            ;
        }

        return $resource->withOperations($operations);
    }
}

<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Initializer;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Form\Type\Resource\FilterType;
use LAG\AdminBundle\Metadata\Action;
use LAG\AdminBundle\Metadata\Application;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\EntityFilter;

final readonly class CollectionOperationInitializer implements CollectionOperationInitializerInterface
{
    public function __construct(
        private ActionInitializerInterface $actionInitializer,
    ) {
    }

    public function initializeCollectionOperation(Application $application, CollectionOperationInterface $operation): CollectionOperationInterface
    {
        $resource = $operation->getResource();

        if ($resource === null) {
            throw new Exception('The resource should be initialized');
        }

        if ($operation->getContextualActions() === null && $resource->hasOperation('create')) {
            $operation = $operation->withContextualActions([]);
        }

        if ($operation->getItemActions() === null) {
            /** @var CollectionOperationInterface $operation */
            $operation = $operation->withItemActions([]);
        }

        if ($operation->getFilters() === null) {
            $operation = $operation->withFilters([]);
        }

        if ($operation->getFilterForm() === null && \count($operation->getFilters() ?? []) > 0) {
            $operation = $operation
                ->withFilterForm(FilterType::class)
                ->withFilterFormOptions(['operation' => $operation->getFullName()])
            ;
        }

        if ($operation->getCollectionFormOptions() === null) {
            $operation = $operation->withCollectionFormOptions([]);
        }

        if ($operation->getCollectionActions() === null) {
            $collectionActions = [];

            if ($resource->hasOperation('create')) {
                $collectionActions[] = new Action(
                    name: $resource->getOperation('create')->getName(),
                    attributes: ['class' => 'btn-success'],
                    operation: $resource->getOperation('create')->getFullName(),
                    icon: 'plus-circle me-1',
                );
            }
            $operation = $operation->withCollectionActions($collectionActions);
        }
        $initializedCollectionActions = [];

        foreach ($operation->getCollectionActions() as $action) {
            $initializedCollectionActions[] = $this->actionInitializer->initializeAction($operation, $action);
        }
        $operation = $operation->withCollectionActions($initializedCollectionActions);
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

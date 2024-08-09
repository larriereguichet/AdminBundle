<?php

namespace LAG\AdminBundle\EventListener\Operation;

use LAG\AdminBundle\Event\OperationEvent;
use LAG\AdminBundle\Form\Type\Resource\FilterType;
use LAG\AdminBundle\Form\Type\Resource\ResourceHiddenType;
use LAG\AdminBundle\Resource\Metadata\Action;
use LAG\AdminBundle\Resource\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Resource\Metadata\Create;
use LAG\AdminBundle\Resource\Metadata\Index;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use function Symfony\Component\String\u;

final readonly class InitializeCollectionOperationListener
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

        if ($operation->getFilterForm() === null && $operation instanceof Index && \count($operation->getFilters() ?? []) > 0) {
            $operation = $operation->withFilterForm(FilterType::class);
        }

        if (is_a($operation->getFilterForm(), FilterType::class, true)) {
            $operation = $operation->withFilterFormOptions(array_merge([
                'application' => $resource->getApplication(),
                'resource' => $resource->getName(),
                'operation' => $operation->getName(),
            ], $operation->getFilterFormOptions()));
        }

        if ($operation->getForm() === ResourceHiddenType::class) {
            $operation = $operation->withFormOptions([
                'application' => $resource->getApplication(),
                'resource' => $resource->getName(),
                'data_class' => $resource->getDataClass(),
            ]);
        }

        if ($operation->getItemFormOptions() === null) {
            $operation = $operation->withItemFormOptions([]);
        }

        if ($operation->getCollectionFormOptions() === null) {
            $operation = $operation->withCollectionFormOptions([]);
        }

        if ($operation->getCollectionActions() === null) {
            $collectionActions = [];

            if ($resource->hasOperationOfType(Create::class)) {
                $collectionActions[] = new Action(
                    name: $resource->getOperationOfType(Create::class)->getName(),
                    operation: $resource->getOperationOfType(Create::class)->getName(),
                    attributes: ['class' => 'btn-success'],
                    icon: 'bi bi-plus-circle me-1',
                );
            }
            $operation = $operation->withCollectionActions($collectionActions);
        }
        $collectionActions = [];

        foreach ($operation->getCollectionActions() as $action) {
            $collectionActions[] = $this->initializeAction($action, $operation);
        }
        $operation = $operation->withCollectionActions($collectionActions);

        $event->setOperation($operation);
    }

    private function initializeAction(Action $action, OperationInterface $operation): Action
    {
        $resource = $operation->getResource();

        if ($action->getName() === null) {
            $action = $action->withName($action->getOperation());
        }

        if ($action->getResource() === null) {
            $action = $action->withResource($operation->getResource()->getName());
        }

        if ($action->getApplication() === null) {
            $action = $action->withApplication($operation->getResource()->getApplication());
        }

        if ($action->getLabel() === null) {
            if ($operation->getResource()->getTranslationDomain()) {
                $action = $action->withLabel(
                    u('{application}.{resource}.{operation}')
                        ->replace('{application}', $resource->getApplication())
                        ->replace('{resource}', $resource->getName())
                        ->replace('{operation}', $action->getOperation())
                        ->toString()
                );

            } else {
                $action = $action->withLabel(u($operation->getName())->title()->toString());
            }
        }

        if ($action->getTranslationDomain() === null && $action->isTranslatable()) {
            $action = $action->withTranslationDomain($operation->getResource()->getTranslationDomain());
        }

        return $action;
    }
}
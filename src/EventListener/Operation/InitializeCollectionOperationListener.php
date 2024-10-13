<?php

declare(strict_types=1);

namespace LAG\AdminBundle\EventListener\Operation;

use LAG\AdminBundle\Event\OperationEvent;
use LAG\AdminBundle\Form\Type\Data\HiddenDataType;
use LAG\AdminBundle\Form\Type\Resource\FilterType;
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

        if ($operation->getForm() === HiddenDataType::class) {
            /** @var CollectionOperationInterface $operation */
            $operation = $operation->withFormOptions([
                'application' => $resource->getApplication(),
                'resource' => $resource->getName(),
                'operation' => $operation->getName(),
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
                    icon: 'plus-circle me-1',
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
            /** @var Action $action */
            $action = $action->withName($action->getOperation());
        }

        if ($action->getResource() === null) {
            /** @var Action $action */
            $action = $action->withResource($operation->getResource()->getName());
        }

        if ($action->getApplication() === null) {
            /** @var Action $action */
            $action = $action->withApplication($operation->getResource()->getApplication());
        }

        if ($action->getLabel() === null) {
            if ($operation->getResource()->getTranslationDomain()) {
                /** @var Action $action */
                $action = $action->withLabel(
                    u($operation->getResource()->getTranslationPattern() ?? '{application}.{resource}.{message}')
                        ->replace('{application}', $resource->getApplication())
                        ->replace('{resource}', $resource->getName())
                        ->replace('{operation}', $action->getOperation())
                        ->replace('{message}', $action->getOperation())
                        ->toString()
                );
            } else {
                /** @var Action $action */
                $action = $action->withLabel(u($operation->getName())->title()->toString());
            }
        }

        if ($action->getTranslationDomain() === null && $action->isTranslatable()) {
            /** @var Action $action */
            $action = $action->withTranslationDomain($operation->getResource()->getTranslationDomain());
        }

        return $action;
    }
}

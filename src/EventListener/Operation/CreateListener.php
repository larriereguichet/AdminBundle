<?php

namespace LAG\AdminBundle\EventListener\Operation;

use LAG\AdminBundle\Event\OperationEvent;
use LAG\AdminBundle\Form\Type\OperationDataType;
use LAG\AdminBundle\Form\Type\ResourceFilterType;
use LAG\AdminBundle\Metadata\Action;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\Create;
use LAG\AdminBundle\Metadata\Index;
use LAG\AdminBundle\Metadata\Update;
use LAG\AdminBundle\Routing\Route\RouteNameGeneratorInterface;

class CreateListener
{
    public function __construct(
        private RouteNameGeneratorInterface $routeNameGenerator,
    ) {
    }

    public function __invoke(OperationEvent $event): void
    {
        $operation = $event->getOperation();
        $resource = $event->getResource();

        if (!$operation->getItemActions()) {
            $actions = [];

            if ($operation instanceof CollectionOperationInterface) {
                if ($resource->hasOperation('update')) {
                    $actions[] = new Action(
                        resourceName: $resource->getName(),
                        operationName: 'update',
                        label: 'lag_admin.resource.update',
                        type: 'secondary'
                    );
                }

                if ($resource->hasOperation('delete')) {
                    $actions[] = new Action(
                        resourceName: $resource->getName(),
                        operationName: 'delete',
                        label: 'lag_admin.resource.delete',
                        type: 'danger'
                    );
                }
            } else {
                if ($resource->hasOperation('index')) {
                    $actions[] = new Action(
                        resourceName: $resource->getName(),
                        operationName: 'index',
                        label: 'lag_admin.ui.cancel',
                        type: 'light',
                    );
                }
            }
            $operation = $operation->withItemActions($actions);
        }

        if ($operation instanceof CollectionOperationInterface && !$operation->getListActions()) {
            if ($resource->hasOperation('create')) {
                $operation = $operation->withListActions([new Action(
                    resourceName: $resource->getName(),
                    operationName: 'create',
                    label: 'lag_admin.ui.create',
                    type: 'primary',
                )]);
            }
        }

        if (!$operation->getTargetRoute()) {
            if ($resource->hasOperation('index')) {
                $operation = $operation->withTargetRoute(
                    $this->routeNameGenerator->generateRouteName($resource, $resource->getOperation('index')),
                );
            }
        }

        if (!$operation->getFormType()) {
            if ($operation instanceof Create || $operation instanceof Update) {
                $operation = $operation
                    ->withFormType(OperationDataType::class)
                    ->withFormOptions(['exclude' => $resource->getIdentifiers()])
                ;
            }

            if ($operation instanceof Index && count($operation->getFilters() ?? []) > 0) {
                $operation = $operation
                    ->withFormType(ResourceFilterType::class)
                ;
            }
        }

        if (is_a($operation->getFormType(), ResourceFilterType::class, true) && !array_key_exists('operation', $operation->getFormOptions())) {
            $operation = $operation->withFormOptions([
                'operation' => $operation,
            ]);
        }

        if ($operation instanceof Index) {
            if ($operation->getListActions() === null) {
                $operation = $operation->withListActions([]);
            }
        }

        $event->setOperation($operation);
    }
}

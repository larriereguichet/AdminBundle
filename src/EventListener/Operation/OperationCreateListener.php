<?php

declare(strict_types=1);

namespace LAG\AdminBundle\EventListener\Operation;

use LAG\AdminBundle\Event\Events\OperationEvent;
use LAG\AdminBundle\Form\Type\OperationDataType;
use LAG\AdminBundle\Form\Type\ResourceFilterType;
use LAG\AdminBundle\Metadata\AdminResource;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\Create;
use LAG\AdminBundle\Metadata\Index;
use LAG\AdminBundle\Metadata\Link;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Metadata\Update;

class OperationCreateListener
{
    public function __invoke(OperationEvent $event): void
    {
        $operation = $event->getOperation();
        $resource = $operation->getResource();

        if (!$operation->getFormType()) {
            if ($operation instanceof Create || $operation instanceof Update) {
                $operation = $operation
                    ->withFormType(OperationDataType::class)
                    ->withFormOptions(['exclude' => $resource->getIdentifiers()])
                ;
            }

            if ($operation instanceof Index && \count($operation->getFilters() ?? []) > 0) {
                $operation = $operation->withFormType(ResourceFilterType::class);
            }
        }

        if (is_a($operation->getFormType(), ResourceFilterType::class, true) && !\array_key_exists('operation', $operation->getFormOptions())) {
            $operation = $operation->withFormOptions([
                'operation' => $operation,
            ]);
        }

        if ($operation instanceof CollectionOperationInterface) {
            if ($operation->getFilters() === null) {
                $operation = $operation->withFilters([]);
            }

            if ($operation->getItemActions() === null) {
                $operation = $this->withDefaultItemActions($resource, $operation);
            }
        }
        $event->setOperation($operation);
    }

    private function withDefaultItemActions(AdminResource $resource, CollectionOperationInterface $operation): OperationInterface
    {
        $actions = [];

        if ($resource->hasOperation('update')) {
            $actions[] = new Link(
                resourceName: $resource->getName(),
                operationName: 'update',
                label: 'lag_admin.resource.update',
                type: 'secondary'
            );
        }

        if ($resource->hasOperation('delete')) {
            $actions[] = new Link(
                resourceName: $resource->getName(),
                operationName: 'delete',
                label: 'lag_admin.resource.delete',
                type: 'danger'
            );
        }

        return $operation->withItemActions($actions);
    }
}

<?php

declare(strict_types=1);

namespace LAG\AdminBundle\EventListener\Operation;

use LAG\AdminBundle\Event\Events\OperationEvent;
use LAG\AdminBundle\Form\Type\DataType;
use LAG\AdminBundle\Form\Type\Resource\DeleteType;
use LAG\AdminBundle\Form\Type\Resource\FilterType;
use LAG\AdminBundle\Form\Type\Resource\ResourceType;
use LAG\AdminBundle\Metadata\AdminResource;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\Create;
use LAG\AdminBundle\Metadata\Delete;
use LAG\AdminBundle\Metadata\GetCollection;
use LAG\AdminBundle\Metadata\Link;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Metadata\Update;

class DefaultOperationListener
{
    public function __invoke(OperationEvent $event): void
    {
        $operation = $event->getOperation();
        $resource = $operation->getResource();

        if (!$operation->getFormType()) {
            if ($operation instanceof Create || $operation instanceof Update) {
                if ($resource->getFormType()) {
                    $operation = $operation
                        ->withFormType($resource->getFormType())
                        ->withFormOptions($resource->getFormOptions())
                    ;
                } else {
                    $operation = $operation
                        ->withFormType(DataType::class)
                        ->withFormOptions([
                            'exclude' => $resource->getIdentifiers(),
                            'data_class' => $resource->getDataClass(),
                        ])
                    ;
                }
            }

            if ($operation instanceof GetCollection && \count($operation->getFilters() ?? []) > 0) {
                $operation = $operation->withFormType(FilterType::class);
            }
        }

        if (is_a($operation->getFormType(), FilterType::class, true) && !\array_key_exists('operation', $operation->getFormOptions())) {
            $operation = $operation->withFormOptions([
                'resource' => $resource->getName(),
                'operation' => $operation->getName(),
            ]);
        }

        if (is_a($operation->getFormType(), ResourceType::class, true) && !\array_key_exists('resource', $operation->getFormOptions())) {
            $operation = $operation->withFormOptions(['resource' => $resource->getName()]);
        }

        if ($operation instanceof Delete && $operation->getFormType() === DeleteType::class) {
            $operation = $operation->withFormOptions(array_merge($operation->getFormOptions(), ['resource' => $resource]));
        }

        if ($operation instanceof CollectionOperationInterface) {
            if ($operation->getFilters() === null) {
                $operation = $operation->withFilters([]);
            }
        }

        if (!$operation->getItemActions()) {
            $operation = $this->withDefaultItemActions($resource, $operation);
        }

        if (!$operation->getRedirectRouteParameters()) {
            $operation = $operation->withRedirectRouteParameters([]);
        }

        if ($resource->isValidationEnabled() !== null) {
            if ($operation->isValidationEnabled() === null) {
                $operation = $operation->withValidation($resource->isValidationEnabled());
            }

            if ($resource->getValidationContext() !== null) {
                if ($operation->getValidationContext() === null) {
                    $operation = $operation->withValidationContext($resource->getValidationContext());
                }
            }
        }

        if ($resource->useAjax() !== null) {
            if ($operation->useAjax() === null) {
                $operation = $operation->withAjax($resource->useAjax());
            }

            if ($operation->useAjax()) {
                if ($resource->getNormalizationContext() !== null && $operation->getNormalizationContext() === null) {
                    $operation = $operation->withNormalizationContext($resource->getNormalizationContext());
                }

                if ($resource->getDenormalizationContext() !== null && $operation->getDenormalizationContext() === null) {
                    $operation = $operation->withDenormalizationContext($resource->getDenormalizationContext());
                }
            }
        }

        $event->setOperation($operation);
    }

    private function withDefaultItemActions(AdminResource $resource, OperationInterface $operation): OperationInterface
    {
        $actions = [];

        if ($operation instanceof CollectionOperationInterface) {
            if ($resource->hasOperation('update')) {
                $actions[] = new Link(
                    resourceName: $resource->getName(),
                    operationName: 'update',
                    label: 'lag_admin.ui.update',
                );
            }

            if ($resource->hasOperation('delete')) {
                $actions[] = new Link(
                    resourceName: $resource->getName(),
                    operationName: 'delete',
                    label: 'lag_admin.ui.delete',
                );
            }
        } else {
            if ($resource->hasOperation('index')) {
                $actions[] = new Link(
                    resourceName: $resource->getName(),
                    operationName: 'index',
                    label: 'lag_admin.ui.cancel',
                );
            }
        }

        return $operation->withItemActions($actions);
    }
}

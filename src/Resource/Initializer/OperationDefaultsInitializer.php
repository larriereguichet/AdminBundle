<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Initializer;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Metadata\Application;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\Create;
use LAG\AdminBundle\Metadata\Link;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Metadata\Resource;
use Symfony\Component\String\Inflector\EnglishInflector;

use function Symfony\Component\String\u;

final readonly class OperationDefaultsInitializer implements OperationDefaultsInitializerInterface
{
    public function initializeOperationDefaults(Application $application, OperationInterface $operation): OperationInterface
    {
        $resource = $operation->getResource();

        if ($resource === null) {
            throw new Exception('The resource should be initialized');
        }

        if ($operation->getFullName() === null) {
            $operation = $operation->withName(
                u($resource->getApplication())
                    ->append('.', $resource->getName())
                    ->append('.', $operation->getName())
                    ->lower()
                    ->toString()
            );
        }

        if ($operation->getTitle() === null) {
            $inflector = new EnglishInflector();

            if ($operation instanceof CollectionOperationInterface) {
                $title = u($inflector->pluralize($resource->getName())[0]);
            } else {
                $title = u($operation->getName())
                    ->append(' ')
                    ->append($resource->getName())
                ;
            }
            $operation = $operation->withTitle($title->replace('_', ' ')->title()->trim()->toString());
        }

        if ($operation->getBaseTemplate() === null) {
            $baseTemplate = '@LAGAdmin/base.html.twig';

            if ($application->getBaseTemplate()) {
                $baseTemplate = $application->getBaseTemplate();
            }
            $operation = $operation->withBaseTemplate($operation->isPartial() ? '@LAGAdmin/partial.html.twig' : $baseTemplate);
        }

        if ($operation->getContextualActions() === null) {
            $operation = $operation->withContextualActions([]);
        }

        if ($operation->getItemActions() === null) {
            $operation = $this->initializeItemActions($resource, $operation);
        }

        if ($operation->getRedirectRouteParameters() === null) {
            $operation = $operation->withRedirectRouteParameters([]);
        }

        if ($operation->getRedirectOperation() !== null && !u($operation->getRedirectOperation())->containsAny('.')) {
            $operation = $operation->withRedirectOperation($application->getName().'.'.$resource->getName().'.'.$operation->getRedirectOperation());
        }

        if ($resource->hasValidation()) {
            if ($operation->hasValidation() === null) {
                $operation = $operation->withValidation($resource->hasValidation());
            }

            if (($resource->getValidationContext() !== null) && $operation->getValidationContext() === null) {
                $operation = $operation->withValidationContext($resource->getValidationContext());
            }
        }

        if ($resource->hasAjax()) {
            if ($operation->hasAjax() === null) {
                $operation = $operation->withAjax($resource->hasAjax());
            }

            if ($operation->hasAjax()) {
                if ($resource->getNormalizationContext() !== null && $operation->getNormalizationContext() === null) {
                    $operation = $operation->withNormalizationContext($resource->getNormalizationContext());
                }

                if ($resource->getDenormalizationContext() !== null && $operation->getDenormalizationContext() === null) {
                    $operation = $operation->withDenormalizationContext($resource->getDenormalizationContext());
                }
            }
        }

        if ($operation->getPermissions() === null) {
            $operation = $operation->withPermissions($resource->getPermissions());
        }

        if ($operation->getPermissions() === null) {
            $operation = $operation->withPermissions([]);
        }

        if ($operation->getInput() === null & $resource->getInput() !== null) {
            $operation = $operation->withInput($resource->getInput());
        }

        if ($operation->getOutput() === null & $resource->getOutput() !== null) {
            $operation = $operation->withOutput($resource->getOutput());
        }

        if ($operation->getIdentifiers() === null) {
            if ($operation instanceof Create) {
                $operation = $operation->withIdentifiers([]);
            } else {
                $operation = $operation->withIdentifiers($resource->getIdentifiers());
            }
        }

        return $operation;
    }

    private function initializeItemActions(Resource $resource, OperationInterface $operation): OperationInterface
    {
        $actions = [];

        if ($operation instanceof CollectionOperationInterface) {
            if ($resource->hasOperation('update')) {
                $actions[] = new Link(
                    operation: $resource->getApplication().'.'.$resource->getName().'update',
                    label: 'lag_admin.ui.update',
                );
            }

            if ($resource->hasOperation('delete')) {
                $actions[] = new Link(
                    operation: $resource->getApplication().'.'.$resource->getName().'delete',
                    label: 'lag_admin.ui.delete',
                );
            }
        } else {
            if ($resource->hasOperation('index')) {
                $actions[] = new Link(
                    operation: $resource->getApplication().'.'.$resource->getName().'index',
                    label: 'lag_admin.ui.cancel',
                );
            }
        }

        return $operation->withItemActions($actions);
    }
}

<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Factory;

use LAG\AdminBundle\Metadata\Attribute\Create;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\OperationMetadataInterface;
use Symfony\Component\String\Inflector\EnglishInflector;

use function Symfony\Component\String\u;

final class OperationMetadataFactory implements OperationMetadataFactoryInterface
{
    public function createMetadata(OperationMetadataInterface $operation): OperationMetadataInterface
    {
        $resource = $operation->getResource();
        $application = $resource->getApplication();

        if (u($operation->getName())->containsAny('.')) {
            $operation = $operation->withShortName(
                u($resource->getApplicationName())
                    ->append('.', $resource->getShortName())
                    ->append('.', $operation->getShortName())
                    ->lower()
                    ->toString()
            );
        }

        if ($operation->getTitle() === null) {
            $inflector = new EnglishInflector();

            if ($operation instanceof CollectionOperationInterface) {
                $title = u($inflector->pluralize($resource->getShortName())[0]);
            } else {
                $title = u($operation->getShortName())
                    ->append(' ')
                    ->append($resource->getShortName())
                ;
            }
            $operation = $operation->withTitle($title->replace('_', ' ')->title()->trim()->toString());
        }

        if ($operation->getBaseTemplate() === null) {
            $baseTemplate = $operation->canBeEmbedded() ? '@LAGAdmin/partial.html.twig' : $application->getBaseTemplate();
            $operation = $operation->withBaseTemplate($baseTemplate);
        }

        if ($operation->getRedirectRouteParameters() === null) {
            $operation = $operation->withRedirectRouteParameters([]);
        }

        if ($operation->getRedirectOperation() !== null && !u($operation->getRedirectOperation())->containsAny('.')) {
            $operation = $operation->withRedirectOperation($application->getName().'.'.$resource->getShortName().'.'.$operation->getRedirectOperation());
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
}

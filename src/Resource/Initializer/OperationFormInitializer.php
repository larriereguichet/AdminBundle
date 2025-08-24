<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Initializer;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Form\Type\Resource\DeleteType;
use LAG\AdminBundle\Form\Type\Resource\ResourceDataType;
use LAG\AdminBundle\Metadata\Application;
use LAG\AdminBundle\Metadata\Create;
use LAG\AdminBundle\Metadata\Delete;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Metadata\Resource;
use LAG\AdminBundle\Metadata\Update;

final class OperationFormInitializer implements OperationFormInitializeInterface
{
    public function initializeOperationForm(Application $application, OperationInterface $operation): OperationInterface
    {
        $resource = $operation->getResource();

        if ($resource === null) {
            throw new Exception('The resource should be initialized');
        }

        if ($operation->getForm() === null) {
            // When the operation does not define a form, we try to set the resource default form. If none is defined
            // either, we use the generic data form
            if ($operation instanceof Create || $operation instanceof Update) {
                if ($resource->getForm() !== null) {
                    $operation = $operation
                        ->withForm($resource->getForm())
                        ->withFormOptions($resource->getFormOptions())
                    ;
                }

                if ($operation->getForm() === null) {
                    $operation = $operation
                        ->withForm(ResourceDataType::class)
                        ->withFormOptions([
                            'exclude' => $resource->getIdentifiers(),
                            'data_class' => $resource->getResourceClass(),
                            'operation' => $operation->getFullName(),
                        ])
                    ;
                }
            }
        }

        if ($operation->getFormOptions() === null) {
            $operation = $operation->withFormOptions([]);
        }

        if ($operation->getFormOption('translation_domain') === null && $resource->getTranslationDomain() !== null) {
            $operation = $operation->withFormOption('translation_domain', $resource->getTranslationDomain());
        }

        if ($operation->getFormTemplate() === null && $resource->getFormTemplate() !== null) {
            $operation = $operation->withFormTemplate($resource->getFormTemplate());
        }

        if ($operation instanceof Delete && $operation->getForm() === DeleteType::class && $operation->getFormOption('resource') === null) {
            $operation = $operation->withFormOption('resource', $resource);
        }

        return $operation;
    }
}

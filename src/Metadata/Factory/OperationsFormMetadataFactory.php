<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Factory;

use LAG\AdminBundle\Form\Type\Resource\DeleteType;
use LAG\AdminBundle\Form\Type\Resource\ResourceDataType;
use LAG\AdminBundle\Metadata\Attribute\Create;
use LAG\AdminBundle\Metadata\Attribute\Delete;
use LAG\AdminBundle\Metadata\Attribute\Update;
use LAG\AdminBundle\Metadata\OperationMetadataInterface;
use LAG\AdminBundle\Metadata\ResourceMetadataInterface;

final readonly class OperationsFormMetadataFactory implements ResourceMetadataFactoryInterface
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
                                'operation' => $operation->getName(),
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
            $operations[$operation->getName()] = $operation;
        }

        return $resource->withOperations($operations);
    }
}

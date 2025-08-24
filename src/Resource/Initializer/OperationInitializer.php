<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Initializer;

use LAG\AdminBundle\Metadata\Application;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\OperationInterface;

final readonly class OperationInitializer implements OperationInitializerInterface
{
    public function __construct(
        private OperationDefaultsInitializerInterface $defaultsOperationInitializer,
        private CollectionOperationInitializerInterface $collectionOperationInitializer,
        private OperationFormInitializeInterface $operationFormInitializer,
        private OperationRoutingInitializerInterface $operationRoutingInitializer,
    ) {
    }

    public function initializeOperation(Application $application, OperationInterface $operation): OperationInterface
    {
        $operation = $this->defaultsOperationInitializer->initializeOperationDefaults($application, $operation);

        if ($operation instanceof CollectionOperationInterface) {
            $operation = $this->collectionOperationInitializer->initializeCollectionOperation($application, $operation);
        }
        $operation = $this->operationFormInitializer->initializeOperationForm($application, $operation);

        return $this->operationRoutingInitializer->initializeOperationRouting($application, $operation);
    }
}

<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Factory;

use LAG\AdminBundle\Metadata\OperationMetadataInterface;

final readonly class OperationIdentifiersMetadataFactory implements OperationMetadataFactoryInterface
{
    public function __construct(
        private OperationMetadataFactoryInterface $metadataFactory,
    ) {
    }

    public function createMetadata(OperationMetadataInterface $operation): OperationMetadataInterface
    {
        $operation = $this->metadataFactory->createMetadata($operation);
        $resource = $operation->getResource();

        return $operation->withIdentifiers($operation->getIdentifiers() ?? $resource->getIdentifiers() ?? []);
    }
}

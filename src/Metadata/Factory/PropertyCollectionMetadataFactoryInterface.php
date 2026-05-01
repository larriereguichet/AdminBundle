<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Factory;

use LAG\AdminBundle\Metadata\PropertyMetadataInterface;

interface PropertyCollectionMetadataFactoryInterface
{
    /**
     * @param class-string $resourceClass
     *
     * @return array<PropertyMetadataInterface>
     */
    public function createMetadata(string $resourceClass): array;
}

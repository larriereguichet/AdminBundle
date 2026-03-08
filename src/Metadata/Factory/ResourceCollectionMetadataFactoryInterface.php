<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Factory;

use LAG\AdminBundle\Metadata\ResourceMetadataInterface;

interface ResourceCollectionMetadataFactoryInterface
{
    /**
     * @return array<string, ResourceMetadataInterface>
     */
    public function createMetadata(): array;
}

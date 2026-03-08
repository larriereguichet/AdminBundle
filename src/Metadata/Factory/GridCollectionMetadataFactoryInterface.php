<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Factory;

use LAG\AdminBundle\Metadata\GridMetadataInterface;

interface GridCollectionMetadataFactoryInterface
{
    /**
     * Create a collection of grid metadata. It can come from various source like attributes or configuration files.
     *
     * @return array<string, GridMetadataInterface>
     */
    public function createMetadata(): array;
}

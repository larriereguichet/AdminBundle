<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Factory;

use LAG\AdminBundle\Metadata\GridMetadataInterface;

interface GridMetadataFactoryInterface
{
    /**
     * Create grid metadata for the given grid name. Metadata only holds configuration values and is not yet validated
     * or initialized. This metadata should be passed to the grid factory to create a valid grid instance.
     */
    public function createMetadata(string $gridName): GridMetadataInterface;
}

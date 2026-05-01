<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Factory;

use LAG\AdminBundle\Metadata\ResourceMetadataInterface;

interface ResourceMetadataFactoryInterface
{
    public function createMetadata(string $resourceName): ResourceMetadataInterface;
}

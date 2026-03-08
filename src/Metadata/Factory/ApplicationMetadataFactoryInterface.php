<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Factory;

use LAG\AdminBundle\Metadata\ApplicationMetadataInterface;

interface ApplicationMetadataFactoryInterface
{
    public function createMetadata(string $applicationName): ApplicationMetadataInterface;
}

<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Factory;

use LAG\AdminBundle\Metadata\OperationMetadataInterface;

interface OperationMetadataFactoryInterface
{
    public function createMetadata(OperationMetadataInterface $operation): OperationMetadataInterface;
}

<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Factory;

use LAG\AdminBundle\Metadata\PropertyMetadataInterface;

interface PropertyMetadataFactoryInterface
{
    public function create(PropertyMetadataInterface $property): PropertyMetadataInterface;
}

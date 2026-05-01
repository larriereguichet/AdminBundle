<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Factory;

use LAG\AdminBundle\Metadata\ResourceInterface;

interface ResourceCollectionFactoryInterface
{
    /**
     * Create a collection of validated resources.
     *
     * @return array<ResourceInterface>
     */
    public function create(): array;
}

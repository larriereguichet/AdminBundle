<?php

namespace LAG\AdminBundle\Slug\Mapping;

use LAG\AdminBundle\Entity\Mapping\Sluggable;

interface SlugMappingInterface
{
    /**
     * @param string $resourceClass
     * @return array<int, Sluggable>
     */
    public function getMapping(string $resourceClass): array;

    public function hasMapping(string $resourceClass): bool;
}

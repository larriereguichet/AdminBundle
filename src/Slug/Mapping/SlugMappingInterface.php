<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Slug\Mapping;

use LAG\AdminBundle\Entity\Mapping\Sluggable;

interface SlugMappingInterface
{
    /**
     * @return array<int, Sluggable>
     */
    public function getMapping(string $resourceClass): array;

    public function hasMapping(string $resourceClass): bool;
}

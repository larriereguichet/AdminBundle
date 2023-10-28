<?php

namespace LAG\AdminBundle\Slug\Mapping;

use LAG\AdminBundle\Entity\Mapping\Sluggable;
use LAG\AdminBundle\Metadata\AttributesHelper;
use LAG\AdminBundle\Metadata\Registry\ResourceRegistryInterface;

class SlugMapping implements SlugMappingInterface
{
    /** @var array<string, array<int, Sluggable>> */
    private array $mapping = [];

    public function __construct(
        ResourceRegistryInterface $registry,
    ) {
        foreach ($registry->all() as $resource) {
            $this->mapping[$resource->getDataClass()] = AttributesHelper::getAttributes(
                $resource->getDataClass(),
                Sluggable::class,
            );
        }
    }

    public function hasMapping(string $resourceClass): bool
    {
        return array_key_exists($resourceClass, $this->mapping);
    }

    public function getMapping(string $resourceClass): array
    {
        if (!$this->hasMapping($resourceClass)) {
            return [];
        }

        return $this->mapping[$resourceClass];
    }
}

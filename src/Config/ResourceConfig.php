<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Config;

use LAG\AdminBundle\Metadata\ResourceMetadataInterface;

final class ResourceConfig
{
    /** @var ResourceMetadataInterface[] */
    private array $resources = [];

    public function addResource(string $name, ResourceMetadataInterface $resource): self
    {
        $resource = $resource->withShortName($name);
        $this->resources[$resource->getName()] = $resource;

        return $this;
    }

    /** @return array<string, ResourceMetadataInterface> */
    public function getResources(): array
    {
        return $this->resources;
    }
}

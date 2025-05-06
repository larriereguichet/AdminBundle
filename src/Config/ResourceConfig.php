<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Config;

use LAG\AdminBundle\Config\Mapper\ResourceMapper;
use LAG\AdminBundle\Metadata\Resource;

final class ResourceConfig implements ResourceConfigInterface
{
    /** @var Resource[] */
    private array $resources = [];

    public function addResource(Resource $resource): ResourceConfigInterface
    {
        $this->resources[$resource->getName()] = $resource;

        return $this;
    }

    public function getExtensionAlias(): string
    {
        return 'lag_admin';
    }

    public function toArray(): array
    {
        $output = [];

        foreach ($this->resources as $resource) {
            $output['resources'][] = new ResourceMapper()->toArray($resource);
        }

        return $output;
    }
}

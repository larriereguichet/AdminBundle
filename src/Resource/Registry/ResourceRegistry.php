<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Registry;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Resource\AdminResource;
use LAG\AdminBundle\Resource\Locator\ResourceLocatorInterface;

class ResourceRegistry implements ResourceRegistryInterface
{
    private array $resources = [];

    public function __construct(
        array $resourcesPath,
        ResourceLocatorInterface $locator,
    ) {
        foreach ($resourcesPath as $path) {
            $resources = $locator->locate($path);

            foreach ($resources as $resource) {
                $this->resources[$resource->getName()] = $resource;
            }
        }
    }

    public function has(string $resourceName): bool
    {
        return \array_key_exists($resourceName, $this->resources);
    }

    public function get(string $resourceName): AdminResource
    {
        if (!$this->has($resourceName)) {
            throw new Exception('Resource with name "'.$resourceName.'" not found');
        }

        return $this->resources[$resourceName];
    }

    public function all(): array
    {
        return $this->resources;
    }

    public function getResourceNames(): array
    {
        return array_keys($this->resources);
    }
}

<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Registry;

use LAG\AdminBundle\Admin\Factory\AdminFactoryInterface;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Exception\UnexpectedTypeException;
use LAG\AdminBundle\Metadata\Admin;
use LAG\AdminBundle\Metadata\Locator\MetadataLocatorInterface;

class ResourceRegistry implements ResourceRegistryInterface
{
    private array $resources = [];
    private bool $loaded = false;

    public function __construct(
        private array $resourcePaths,
        private MetadataLocatorInterface $locator,
        private AdminFactoryInterface $adminFactory,
    ) {
    }

    public function load(): void
    {
        if ($this->loaded) {
            return;
        }

        foreach ($this->resourcePaths as $path) {
            $resources = $this->locator->locateCollection($path);

            foreach ($resources as $resource) {
                if (!$resource instanceof Admin) {
                    throw new UnexpectedTypeException($resource, Admin::class);
                }

                if (!$resource->getName()) {
                    throw new Exception('The admin resource has no name');
                }
                $this->resources[$resource->getName()] = $this->adminFactory->create($resource);
            }
        }
        $this->loaded = true;
    }

    public function has(string $resourceName): bool
    {
        return \array_key_exists($resourceName, $this->resources);
    }

    public function get(string $resourceName): Admin
    {
        if (!$this->has($resourceName)) {
            throw new Exception('Resource with name "'.$resourceName.'" not found');
        }

        return $this->resources[$resourceName];
    }

    public function all(): array
    {
        $this->load();

        return $this->resources;
    }

    public function getResourceNames(): array
    {
        return \array_keys($this->resources);
    }
}

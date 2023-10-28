<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Registry;

use LAG\AdminBundle\Metadata\AdminResource;

class CacheRegistryDecorator implements ResourceRegistryInterface
{
    /** @var AdminResource[] */
    private array $cache = [];
    private bool $cacheHydrated = false;

    public function __construct(
        private ResourceRegistryInterface $decorated,
    ) {
    }

    public function get(string $resourceName): AdminResource
    {
        $this->hydrateCache();

        return $this->cache[$resourceName];
    }

    public function all(): iterable
    {
        $this->hydrateCache();

        return $this->cache;
    }

    public function has(string $resourceName): bool
    {
        return $this->decorated->has($resourceName);
    }

    private function hydrateCache(): void
    {
        if ($this->cacheHydrated) {
            return;
        }

        foreach ($this->decorated->all() as $resource) {
            $this->cache[$resource->getName()] = $resource;
        }
        $this->cacheHydrated = true;
    }
}

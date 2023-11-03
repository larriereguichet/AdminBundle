<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Registry;

use LAG\AdminBundle\Metadata\AdminResource;

class CacheRegistry implements ResourceRegistryInterface
{
    /** @var array<string, array<, string, AdminResource> */
    private array $cache = [];

    public function __construct(
        private ResourceRegistryInterface $registry,
        private string $defaultApplicationName,
    ) {
    }

    public function get(string $resourceName, ?string $applicationName = null): AdminResource
    {
        $applicationName = $applicationName ?? $this->defaultApplicationName;

        if (!isset($this->cache[$applicationName][$resourceName])) {
            $this->cache[$applicationName][$resourceName] = $this->registry->get($resourceName, $applicationName);
        }

        return $this->cache[$applicationName][$resourceName];
    }

    public function all(): iterable
    {
        return $this->registry->all();
    }

    public function has(string $resourceName, ?string $applicationName = null): bool
    {
        return $this->registry->has($resourceName, $applicationName);
    }
}

<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Registry;

use LAG\AdminBundle\Resource\Metadata\Resource;

class CacheRegistry implements ResourceRegistryInterface
{
    /** @var array<string, array<string, Resource>> */
    private array $cache = [];

    public function __construct(
        private readonly ResourceRegistryInterface $registry,
        private readonly string $defaultApplication,
    ) {
    }

    public function get(string $resourceName, ?string $applicationName = null): Resource
    {
        $applicationName = $applicationName ?? $this->defaultApplication;

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

<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Factory;

use LAG\AdminBundle\Metadata\Resource;

final class CacheResourceFactory implements ResourceFactoryInterface
{
    private array $cache = [];

    public function __construct(
        private readonly ResourceFactoryInterface $resourceFactory,
    ) {
    }

    public function create(string $resourceName): Resource
    {
        if (empty($this->cache[$resourceName])) {
            $this->cache[$resourceName] = $this->resourceFactory->create($resourceName);
        }

        return $this->cache[$resourceName];
    }
}

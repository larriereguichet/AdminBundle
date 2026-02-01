<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Factory;

use LAG\AdminBundle\Metadata\Attribute\Resource;
use Symfony\Contracts\Cache\CacheInterface;

final readonly class CacheResourceFactory implements ResourceFactoryInterface
{
    public function __construct(
        private ResourceFactoryInterface $resourceFactory,
        private CacheInterface $cache,
    ) {
    }

    public function create(string $resourceName): Resource
    {
        return $this->cache->get($resourceName, fn () => $this->resourceFactory->create($resourceName));
    }
}

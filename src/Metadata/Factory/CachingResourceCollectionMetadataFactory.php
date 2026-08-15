<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Factory;

use Symfony\Contracts\Cache\CacheInterface;

final readonly class CachingResourceCollectionMetadataFactory implements ResourceCollectionMetadataFactoryInterface
{
    public function __construct(
        private ResourceCollectionMetadataFactoryInterface $decorated,
        private CacheInterface $cache,
    ) {
    }

    public function createMetadata(): array
    {
        return $this->cache->get('lag_admin.resources', fn () => $this->decorated->createMetadata());
    }
}

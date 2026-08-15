<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Factory;

use Symfony\Contracts\Cache\CacheInterface;

final readonly class CachingGridCollectionMetadataFactory implements GridCollectionMetadataFactoryInterface
{
    public function __construct(
        private GridCollectionMetadataFactoryInterface $decorated,
        private CacheInterface $cache,
    ) {
    }

    public function createMetadata(): array
    {
        return $this->cache->get('lag_admin.grids', fn () => $this->decorated->createMetadata());
    }
}

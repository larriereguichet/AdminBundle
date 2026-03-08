<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Factory;

final readonly class GridCollectionMetadataFactory implements GridCollectionMetadataFactoryInterface
{
    /** @param array<string> $paths */
    public function __construct(
        private array $paths,
    ) {
    }

    public function createMetadata(): array
    {
        foreach ($this->paths as $path) {
            // TODO configuration files
            throw new \RuntimeException();
        }
        throw new \RuntimeException();
    }
}

<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Bridge\Flysystem\Registry;

use LAG\AdminBundle\Exception\Exception;
use League\Flysystem\FilesystemOperator;

final readonly class StorageRegistry implements StorageRegistryInterface
{
    public function __construct(
        /** @var iterable<string, FilesystemOperator> $storages */
        private iterable $storages = [],
    ) {
    }

    public function get(string $storageName): FilesystemOperator
    {
        foreach ($this->storages as $name => $storage) {
            if ($storageName === $name) {
                return $storage;
            }
        }

        throw new Exception(\sprintf('The storage "%s" does not exist.', $storageName));
    }

    public function has(string $storageName): bool
    {
        foreach ($this->storages as $name => $storage) {
            if ($storageName === $name) {
                return true;
            }
        }

        return false;
    }
}

<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Bridge\Flysystem\Registry;

use League\Flysystem\FilesystemOperator;

interface StorageRegistryInterface
{
    public function get(string $storageName): FilesystemOperator;

    public function has(string $storageName): bool;
}

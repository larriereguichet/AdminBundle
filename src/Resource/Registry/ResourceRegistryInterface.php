<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Registry;

use LAG\AdminBundle\Metadata\Admin;

interface ResourceRegistryInterface
{
    /**
     * Return true if the registry contains a resource with the given name.
     */
    public function has(string $resourceName): bool;

    /**
     * Get a resource from the registry. If no resource match the given name, an exception will be thrown.
     */
    public function get(string $resourceName): Admin;

    /**
     * Return an array of the registry resources.
     *
     * @return Admin[]
     */
    public function all(): array;

    /**
     * Return an array of the registry resources names.
     */
    public function getResourceNames(): array;
}

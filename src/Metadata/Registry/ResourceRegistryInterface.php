<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Registry;

use LAG\AdminBundle\Metadata\AdminResource;

interface ResourceRegistryInterface
{
    /**
     * Get a resource from the registry. If no resource match the given name, an exception will be thrown.
     */
    public function get(string $resourceName): AdminResource;

    /**
     * Return true if the registry contains a resource with the given name.
     */
    public function has(string $resourceName): bool;

    /**
     * Return an array of the registry resources.
     *
     * @return iterable<AdminResource>
     */
    public function all(): iterable;
}

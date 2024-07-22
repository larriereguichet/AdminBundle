<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Registry;

use LAG\AdminBundle\Resource\Metadata\Resource;

/**
 * Store resources by name and application. Each resource name should be unique by application.
 */
interface ResourceRegistryInterface
{
    /**
     * Return a resource from the registry. If no resource match the given name, an exception will be thrown.
     */
    public function get(string $resourceName, ?string $applicationName = null): Resource;

    /**
     * Return true if the registry contains a resource with the given name.
     */
    public function has(string $resourceName, ?string $applicationName = null): bool;

    /**
     * Return an array of the registry resources.
     *
     * @return iterable<Resource>
     */
    public function all(): iterable;
}

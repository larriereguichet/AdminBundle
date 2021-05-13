<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Admin\Resource\Registry;

use LAG\AdminBundle\Admin\Resource\AdminResource;
use LAG\AdminBundle\Exception\Exception;

interface ResourceRegistryInterface
{
    /**
     * Add a resource to the registry.
     */
    public function add(AdminResource $resource): void;

    /**
     * Remove a resource from the registry. If no resource match the given name, an exception will be thrown.
     *
     * @throws Exception
     */
    public function remove(string $resourceName): void;

    /**
     * Return true if the registry contains a resource with the given name.
     */
    public function has(string $resourceName): bool;

    /**
     * Get a resource from the registry. If no resource match the given name, an exception will be thrown.
     */
    public function get(string $resourceName): AdminResource;

    /**
     * Return an array of the registry resources.
     *
     * @return AdminResource[]
     */
    public function all(): array;

    /**
     * Return an array of the registry resources names.
     */
    public function keys(): array;
}

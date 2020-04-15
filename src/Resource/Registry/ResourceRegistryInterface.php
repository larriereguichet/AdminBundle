<?php

namespace LAG\AdminBundle\Resource\Registry;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Resource\AdminResource;

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
     *
     * @param $resourceName
     */
    public function get($resourceName): AdminResource;

    /**
     * Return an array of the registry resources.
     */
    public function all(): array;

    /**
     * Return an array of the registry resources names.
     */
    public function keys(): array;
}
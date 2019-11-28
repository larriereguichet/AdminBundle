<?php

namespace LAG\AdminBundle\DataProvider;

use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Exception\Exception;

/**
 * Generic data provider interface.
 */
interface DataProviderInterface
{
    /**
     * Return a collection of entities.
     *
     * @return mixed
     */
    public function getCollection(AdminInterface $admin, array $filters = []);

    /**
     * Return a single entity. Throw an exception if no entity was found.
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function get(AdminInterface $admin, string $identifier);

    /**
     * Save an entity loaded into an admin..
     */
    public function save(AdminInterface $admin): void;

    /**
     * Create a new entity for the given admin. Return the created entity.
     *
     * @return mixed
     */
    public function create(AdminInterface $admin);

    /**
     * Delete an existing entity in the given admin. Throws an exception if there is no loaded entities in the admin.
     *
     * @throws Exception
     */
    public function delete(AdminInterface $admin): void;
}

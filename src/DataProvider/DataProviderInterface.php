<?php

namespace LAG\AdminBundle\DataProvider;

use LAG\AdminBundle\Admin\AdminInterface;

/**
 * Generic data provider interface
 */
interface DataProviderInterface
{
    /**
     * Return a collection of entities.
     *
     * @param AdminInterface $admin
     *
     * @return mixed
     */
    public function getCollection(AdminInterface $admin);

    /**
     * Return a single entity.
     *
     * @param AdminInterface $admin
     * @param string         $identifier
     *
     * @return mixed
     */
    public function getItem(AdminInterface $admin, string $identifier);

    /**
     * Save an entity loaded into an admin..
     *
     * @param AdminInterface $admin
     */
    public function saveItem(AdminInterface $admin);
}

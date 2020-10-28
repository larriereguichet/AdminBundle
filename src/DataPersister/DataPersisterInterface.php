<?php

namespace LAG\AdminBundle\DataPersister;

use LAG\AdminBundle\Exception\Exception;

interface DataPersisterInterface
{
    /**
     * Save an entity loaded into an admin.
     */
    public function save(object $data): void;

    /**
     * Delete an existing entity in the given admin. Throws an exception if there is no loaded entities in the admin.
     *
     * @throws Exception
     */
    public function delete(object $data): void;
}

<?php

namespace LAG\AdminBundle\Admin\Helper;

use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Exception\Exception;

interface AdminHelperInterface
{
    /**
     * Define the current admin based on the request parameters.
     */
    public function setAdmin(AdminInterface $admin): void;

    /**
     * Returns the current admin.
     *
     * @throws Exception An exception is thrown if no admin has been defined
     */
    public function getAdmin(): AdminInterface;

    /**
     * Return true if the current admin has been defined.
     */
    public function hasAdmin(): bool;
}

<?php

namespace LAG\AdminBundle\Admin;

interface AdminAwareInterface
{
    /**
     * Define the current admin.
     */
    public function setAdmin(AdminInterface $admin): void;
}

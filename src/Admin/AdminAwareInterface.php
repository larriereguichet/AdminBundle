<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Admin;

interface AdminAwareInterface
{
    /**
     * Define the current admin.
     */
    public function setAdmin(AdminInterface $admin): void;
}

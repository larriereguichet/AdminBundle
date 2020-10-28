<?php

namespace LAG\AdminBundle\Admin\Helper;

use LAG\AdminBundle\Admin\AdminInterface;

interface AdminHelperInterface
{
    public function setAdmin(AdminInterface $admin): void;

    public function getAdmin(): AdminInterface;

    public function hasAdmin(): bool;
}

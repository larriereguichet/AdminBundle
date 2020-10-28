<?php

namespace LAG\AdminBundle\Admin;

interface AdminAwareInterface
{
    public function setAdmin(AdminInterface $admin): void;
}

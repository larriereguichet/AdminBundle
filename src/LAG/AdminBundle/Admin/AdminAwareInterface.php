<?php

namespace LAG\AdminBundle\Admin;

interface AdminAwareInterface
{
    /**
     * @param AdminInterface $admin
     */
    public function setAdmin(AdminInterface $admin);
    
    /**
     * @return AdminInterface
     */
    public function getAdmin();
}

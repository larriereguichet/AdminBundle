<?php

namespace LAG\AdminBundle\Admin\Behaviors;

use LAG\AdminBundle\Admin\AdminInterface;

trait AdminAwareTrait
{
    /**
     * @var AdminInterface
     */
    protected $admin;
    
    /**
     * @param AdminInterface $admin
     */
    public function setAdmin(AdminInterface $admin)
    {
        $this->admin = $admin;
    }
    
    /**
     * @return AdminInterface
     */
    public function getAdmin()
    {
        return $this->admin;
    }
}

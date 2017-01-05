<?php

namespace LAG\AdminBundle\Admin\Event;

use LAG\AdminBundle\Admin\AdminInterface;
use Symfony\Component\EventDispatcher\Event;

class AdminInjectedEvent extends Event
{
    /**
     * @var AdminInterface
     */
    protected $admin;
    
    /**
     * @var mixed
     */
    protected $controller;
    
    public function __construct(AdminInterface $admin, $controller)
    {
        $this->admin = $admin;
        $this->controller = $controller;
    }
    
    /**
     * @return AdminInterface
     */
    public function getAdmin()
    {
        return $this->admin;
    }
    
    /**
     * @return mixed
     */
    public function getController()
    {
        return $this->controller;
    }
}

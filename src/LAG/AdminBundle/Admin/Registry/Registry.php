<?php

namespace LAG\AdminBundle\Admin\Registry;

use Exception;
use LAG\AdminBundle\Admin\AdminInterface;

/**
 * Admin registry to store Admins along the request.
 */
class Registry
{
    /**
     * Admins registry.
     *
     * @var AdminInterface[]
     */
    protected $admins = [];

    /**
     * Add an Admin to the registry.
     *
     * @param AdminInterface $admin
     * @throws Exception
     */
    public function add(AdminInterface $admin)
    {
        if (array_key_exists($admin->getName(), $this->admins)) {
            throw new Exception('An Admin with the name "'.$admin->getName().'" has already been registered');
        }
        $this->admins[$admin->getName()] = $admin;
    }

    /**
     * Return an Admin from the registry.
     *
     * @param string $name
     * @return AdminInterface
     * @throws Exception
     */
    public function get($name)
    {
        if (!array_key_exists($name, $this->admins)) {
            throw new Exception('No Admin with the name "'.$name.'" has been found');
        }

        return $this->admins[$name];
    }

    /**
     * Return true if an Admin with the name $name has been registered.
     *
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return array_key_exists($name, $this->admins);
    }

    /**
     * Return all the registered Admins.
     *
     * @return AdminInterface[]
     */
    public function all()
    {
        return $this->admins;
    }
}

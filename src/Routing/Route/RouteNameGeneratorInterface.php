<?php

namespace LAG\AdminBundle\Routing\Route;

use LAG\AdminBundle\Metadata\Admin;
use LAG\AdminBundle\Metadata\OperationInterface;

interface RouteNameGeneratorInterface
{
    /**
     * Return a route name according to the given admin and action names, using the routing pattern configured in the
     * application.
     */
    public function generateRouteName(Admin $admin, OperationInterface $operation): string;
}

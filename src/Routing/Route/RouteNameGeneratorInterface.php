<?php

namespace LAG\AdminBundle\Routing\Route;

interface RouteNameGeneratorInterface
{
    /**
     * Return a route name according to the given admin and action names, using the routing pattern configured in the
     * application.
     */
    public function generateRouteName(string $adminName, string $actionName): string;
}

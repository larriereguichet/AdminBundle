<?php

namespace LAG\AdminBundle\Routing\Loader;

use LAG\AdminBundle\Admin\Resource\AdminResource;
use Symfony\Component\Routing\RouteCollection;

interface ResourceLoaderInterface
{
    public function loadRoutes(AdminResource $resource, RouteCollection $routes): void;
}

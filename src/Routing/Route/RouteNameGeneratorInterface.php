<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Routing\Route;

use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use LAG\AdminBundle\Resource\Metadata\Resource;

interface RouteNameGeneratorInterface
{
    /**
     * Return a route name according to the given admin and action names, using the routing pattern configured in the
     * application.
     */
    public function generateRouteName(Resource $resource, OperationInterface $operation): string;
}

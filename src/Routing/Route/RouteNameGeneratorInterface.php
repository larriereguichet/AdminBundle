<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Routing\Route;

use LAG\AdminBundle\Metadata\AdminResource;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;

interface RouteNameGeneratorInterface
{
    /**
     * Return a route name according to the given admin and action names, using the routing pattern configured in the
     * application.
     */
    public function generateRouteName(AdminResource $resource, OperationInterface $operation): string;
}

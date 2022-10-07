<?php

namespace LAG\AdminBundle\Routing\Route;

use LAG\AdminBundle\Metadata\AdminResource;
use LAG\AdminBundle\Metadata\OperationInterface;
use function Symfony\Component\String\u;

class RouteNameGenerator implements RouteNameGeneratorInterface
{
    public function generateRouteName(AdminResource $admin, OperationInterface $operation): string
    {
        return u($admin->getRoutePattern())
            ->replace('{resource}', $admin->getName())
            ->replace('{operation}', $operation->getName())
            ->lower()
            ->toString()
        ;
    }
}

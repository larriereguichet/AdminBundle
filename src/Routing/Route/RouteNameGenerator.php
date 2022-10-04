<?php

namespace LAG\AdminBundle\Routing\Route;

use LAG\AdminBundle\Metadata\Admin;
use LAG\AdminBundle\Metadata\OperationInterface;
use function Symfony\Component\String\u;

class RouteNameGenerator implements RouteNameGeneratorInterface
{
    public function generateRouteName(Admin $admin, OperationInterface $operation): string
    {
        return u($admin->getRoutePattern())
            ->replace('{resource}', $admin->getName())
            ->replace('{operation}', $operation->getName())
            ->lower()
            ->toString()
        ;
    }
}

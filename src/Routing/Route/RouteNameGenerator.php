<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Routing\Route;

use LAG\AdminBundle\Metadata\AdminResource;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;

use function Symfony\Component\String\u;

class RouteNameGenerator implements RouteNameGeneratorInterface
{
    public function generateRouteName(AdminResource $resource, OperationInterface $operation): string
    {
        return u($resource->getRoutePattern())
            ->replace('{application}', $resource->getApplicationName())
            ->replace('{resource}', $resource->getName())
            ->replace('{operation}', $operation->getName())
            ->lower()
            ->toString()
        ;
    }
}

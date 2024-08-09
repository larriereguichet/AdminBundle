<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Routing\Route;

use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use LAG\AdminBundle\Resource\Metadata\Resource;

use function Symfony\Component\String\u;

class RouteNameGenerator implements RouteNameGeneratorInterface
{
    public function generateRouteName(Resource $resource, OperationInterface $operation): string
    {
        return u($resource->getRoutePattern())
            ->replace('{application}', $resource->getApplication())
            ->replace('{resource}', $resource->getName())
            ->replace('{operation}', $operation->getName())
            ->lower()
            ->toString()
        ;
    }
}

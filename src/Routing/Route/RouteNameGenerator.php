<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Routing\Route;

use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Metadata\Resource;

use function Symfony\Component\String\u;

final readonly class RouteNameGenerator implements RouteNameGeneratorInterface
{
    public function generateRouteName(Resource $resource, OperationInterface $operation): string
    {
        return u($resource->getRoutePattern())
            ->replace('{application}', $resource->getApplication())
            ->replace('{resource}', $resource->getName())
            ->replace('{operation}', $operation->getShortName())
            ->lower()
            ->toString()
        ;
    }
}

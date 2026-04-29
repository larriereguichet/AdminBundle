<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Routing\Route;

use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Metadata\ResourceInterface;

use function Symfony\Component\String\u;

final readonly class RouteNameGenerator implements RouteNameGeneratorInterface
{
    public function generateRouteName(ResourceInterface $resource, OperationInterface $operation): string
    {
        return u($resource->getRoutePattern())
            ->replace('{application}', $resource->getApplication())
            ->replace('{resource}', $resource->getShortName())
            ->replace('{operation}', $operation->getShortName())
            ->lower()
            ->toString()
        ;
    }
}

<?php

namespace LAG\AdminBundle\Metadata;

readonly class RouteUrl implements Url
{
    public function __construct(
        private string $route,
        private array $routeParameters = [],
    ) {
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    public function getRouteParameters(): array
    {
        return $this->routeParameters;
    }
}

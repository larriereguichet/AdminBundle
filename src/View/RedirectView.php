<?php

declare(strict_types=1);

namespace LAG\AdminBundle\View;

class RedirectView implements ViewInterface
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

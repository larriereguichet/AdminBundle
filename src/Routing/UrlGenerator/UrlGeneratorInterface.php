<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Routing\UrlGenerator;

interface UrlGeneratorInterface
{
    /**
     * Generate an url for an admin and an action. Route parameters will be mapped to to the property of the given
     * data object.
     */
    public function generate(
        string $adminName,
        string $actionName,
        array $routeParameters = [],
        object $data = null
    ): string;

    /**
     * Generate an url for a route name. Route parameters will be mapped to to the property of the given data object.
     */
    public function generateFromRouteName(string $routeName, array $routeParameters = [], object $data = null): string;
}

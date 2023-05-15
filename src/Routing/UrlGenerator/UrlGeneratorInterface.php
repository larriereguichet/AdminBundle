<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Routing\UrlGenerator;

interface UrlGeneratorInterface
{
    /**
     * Generate an url for a route name. Route parameters will be mapped to the property of the given data object.
     *
     * @param string $routeName The route to generate url
     * @param array<int, string> $routeParameters Optional parameters for url
     * @param mixed $data Data to provide the url parameters
     */
    public function generateFromRouteName(
        string $routeName,
        array $routeParameters = [],
        mixed $data = null
    ): string;

    public function generateFromOperationName(
        string $resourceName,
        string $operationName,
        mixed $data = null
    ): string;
}

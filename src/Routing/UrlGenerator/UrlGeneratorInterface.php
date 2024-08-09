<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Routing\UrlGenerator;

use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use LAG\AdminBundle\Resource\Metadata\Url;

interface UrlGeneratorInterface
{
    public function generateOperationUrl(OperationInterface $operation, mixed $data = null): string;

    public function generateUrl(Url $url, mixed $data = null): string;

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
        mixed $data = null,
        ?string $applicationName = null,
    ): string;
}

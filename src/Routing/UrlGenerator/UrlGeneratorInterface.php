<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Routing\UrlGenerator;

use LAG\AdminBundle\Metadata\Admin;
use LAG\AdminBundle\Metadata\Operation;
use LAG\AdminBundle\Metadata\OperationInterface;

interface UrlGeneratorInterface
{
    /**
     * Generate an url for an admin and an action. Route parameters will be mapped to the property of the given
     * data object.
     */
    public function generatePath(
        Admin $resource,
        OperationInterface $operation,
    ): string;

    /**
     * Generate an url for a route name. Route parameters will be mapped to the property of the given data object.
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

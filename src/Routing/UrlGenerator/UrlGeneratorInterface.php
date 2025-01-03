<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Routing\UrlGenerator;

use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use LAG\AdminBundle\Resource\Metadata\Url;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface as SymfonyUrlGeneratorInterface;

interface UrlGeneratorInterface
{
    public function generate(
        OperationInterface $operation,
        mixed $data = null,
        int $referenceType = SymfonyUrlGeneratorInterface::ABSOLUTE_PATH,
    ): string;

    public function generateFromUrl(
        Url $url,
        mixed $data = null,
        int $referenceType = SymfonyUrlGeneratorInterface::ABSOLUTE_PATH,
    ): string;

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
        mixed $data = null,
        int $referenceType = SymfonyUrlGeneratorInterface::ABSOLUTE_PATH,
    ): string;

    public function generateFromOperationName(
        string $resourceName,
        string $operationName,
        mixed $data = null,
        ?string $applicationName = null,
        int $referenceType = SymfonyUrlGeneratorInterface::ABSOLUTE_PATH,
    ): string;
}

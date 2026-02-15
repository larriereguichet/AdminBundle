<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Routing\UrlGenerator;

use LAG\AdminBundle\Metadata\Attribute\Url;
use LAG\AdminBundle\Metadata\OperationInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

interface ResourceUrlGeneratorInterface
{
    public function generate(OperationInterface $operation, mixed $data = null, int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): string;

    public function generateFromUrl(Url $url, mixed $data = null, int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): string;

    /**
     * Generate a url for a route name. Route parameters will be mapped to the property of the given data object.
     *
     * @param string $routeName The route to generate url
     * @param array<int|string, mixed> $routeParameters Optional parameters for url
     * @param mixed $data Data to provide the url parameters
     */
    public function generateFromRouteName(
        string $routeName,
        array $routeParameters = [],
        mixed $data = null,
        int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH,
    ): string;

    public function generateFromOperationName(
        string $operationName,
        mixed $data = null,
        int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH,
    ): string;
}

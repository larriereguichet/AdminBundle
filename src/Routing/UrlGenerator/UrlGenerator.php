<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Routing\UrlGenerator;

use LAG\AdminBundle\Metadata\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Routing\Parameter\ParametersMapper;
use Symfony\Component\Routing\RouterInterface;

class UrlGenerator implements UrlGeneratorInterface
{
    public function __construct(
        private RouterInterface $router,
        private ResourceRegistryInterface $resourceRegistry,
    ) {
    }

    public function generateFromRouteName(string $routeName, array $routeParameters = [], mixed $data = null): string
    {
        $mappedRouteParameters = $routeParameters;

        if ($data !== null) {
            $mappedRouteParameters = (new ParametersMapper())->map($data, $routeParameters);
        }

        return $this->router->generate($routeName, $mappedRouteParameters);
    }

    public function generateFromOperationName(string $resourceName, string $operationName, mixed $data = null): string
    {
        $resource = $this->resourceRegistry->get($resourceName);
        $operation = $resource->getOperation($operationName);

        return $this->generateFromRouteName(
            $operation->getRoute(),
            array_keys($operation->getRouteParameters()),
            $data,
        );
    }
}

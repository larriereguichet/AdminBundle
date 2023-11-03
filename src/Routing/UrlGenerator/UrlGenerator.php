<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Routing\UrlGenerator;

use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Metadata\Registry\ResourceRegistryInterface;
use Symfony\Component\Routing\RouterInterface;

class UrlGenerator implements UrlGeneratorInterface
{
    public function __construct(
        private RouterInterface $router,
        private ParametersMapperInterface $mapper,
        private ResourceRegistryInterface $resourceRegistry,
    ) {
    }

    public function generate(OperationInterface $operation, mixed $data = null): string
    {
        $parameters = $this->mapper->map($data, $operation->getRouteParameters());

        return $this->router->generate($operation->getRoute(), $parameters);
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

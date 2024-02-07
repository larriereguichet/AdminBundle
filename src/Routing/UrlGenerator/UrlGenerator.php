<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Routing\UrlGenerator;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Metadata\Property\Link;
use LAG\AdminBundle\Resource\Registry\ResourceRegistryInterface;
use Symfony\Component\Routing\RouterInterface;

readonly class UrlGenerator implements UrlGeneratorInterface
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

    public function generateLink(Link $link, mixed $data = null): string
    {
        if ($link->getUrl()) {
            return $link->getUrl();
        }

        if ($link->getResourceName() && $link->getOperationName()) {
            return $this->generateFromOperationName(
                $link->getResourceName(),
                $link->getOperationName(),
                $data,
            );
        }

        if ($link->getRoute()) {
            return $this->generateFromRouteName(
                $link->getRoute(),
                $link->getRouteParameters(),
                $data,
            );
        }

        throw new Exception('Unable to generate a route for the given link');
    }

    public function generateFromRouteName(string $routeName, array $routeParameters = [], mixed $data = null): string
    {
        $mappedRouteParameters = $routeParameters;

        if ($data !== null) {
            $mappedRouteParameters = (new ParametersMapper())->map($data, $routeParameters);
        }

        return $this->router->generate($routeName, $mappedRouteParameters);
    }

    public function generateFromOperationName(
        string $resourceName,
        string $operationName,
        mixed $data = null,
        ?string $applicationName = null,
    ): string {
        $resource = $this->resourceRegistry->get($resourceName, $applicationName);
        $operation = $resource->getOperation($operationName);

        return $this->generate($operation, $data);
    }
}

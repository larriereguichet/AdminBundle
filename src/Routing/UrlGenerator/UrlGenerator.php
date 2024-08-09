<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Routing\UrlGenerator;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use LAG\AdminBundle\Resource\Metadata\Url;
use LAG\AdminBundle\Resource\Registry\ResourceRegistryInterface;
use Symfony\Component\Routing\RouterInterface;

final readonly class UrlGenerator implements UrlGeneratorInterface
{
    public function __construct(
        private RouterInterface $router,
        private ParametersMapperInterface $mapper,
        private ResourceRegistryInterface $resourceRegistry,
    ) {
    }

    public function generateOperationUrl(OperationInterface $operation, mixed $data = null): string
    {
        $parameters = $this->mapper->map($data, $operation->getRouteParameters());

        if (\count($parameters) !== \count($operation->getRouteParameters())) {
            throw new Exception(\sprintf('Unable to generate URL for resource "%s" and operation "%s". Expected "%s" route parameters, got "%s"', $operation->getResource()->getName(), $operation->getName(), \count($operation->getRouteParameters()), \count($parameters)));
        }

        return $this->router->generate($operation->getRoute(), $parameters);
    }

    public function generateUrl(Url $url, mixed $data = null): string
    {
        if ($url->getUrl()) {
            return $url->getUrl();
        }

        if ($url->getResource() && $url->getOperation()) {
            return $this->generateFromOperationName(
                $url->getResource(),
                $url->getOperation(),
                $data,
                $url->getApplication(),
            );
        }

        if ($url->getRoute()) {
            return $this->generateFromRouteName(
                $url->getRoute(),
                $url->getRouteParameters(),
                $data,
            );
        }

        throw new Exception(\sprintf('Unable to generate a route for the action "%s".', $url->getName()));
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

        return $this->generateOperationUrl($operation, $data);
    }
}

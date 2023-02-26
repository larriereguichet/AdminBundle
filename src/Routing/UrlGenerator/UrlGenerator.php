<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Routing\UrlGenerator;

use LAG\AdminBundle\Metadata\AdminResource;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Resource\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Routing\Parameter\ParametersMapper;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\String\Inflector\EnglishInflector;

use function Symfony\Component\String\u;

class UrlGenerator implements UrlGeneratorInterface
{
    public function __construct(
        private RouterInterface $router,
        private ResourceRegistryInterface $resourceRegistry,
    ) {
    }

    public function generatePath(
        AdminResource $resource,
        OperationInterface $operation,
    ): string {
        $resource = $operation->getResource();
        $resourceName = (new EnglishInflector())->pluralize($resource->getName())[0];

        $path = u($resource->getRoutePrefix())
            ->replace('{resourceName}', $resourceName)
        ;

        foreach ($operation->getRouteParameters() as $parameter => $requirement) {
            $path = $path
                ->append('/')
                ->append('{'.$parameter.'}')
            ;
        }
        $operationPath = u($operation->getPath());

        if ($operationPath->length() > 0) {
            $operationPath = $operationPath->ensureStart('/');
        }

        return $path
            ->append($operationPath->toString())
            ->toString()
        ;
    }

    public function generateFromRouteName(string $routeName, array $routeParameters = [], mixed $data = null): string
    {
        $mappedRouteParameters = $routeParameters;

        if ($data !== null) {
            $mapper = new ParametersMapper();
            $mappedRouteParameters = $mapper->map($data, $routeParameters);
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

<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Routing\UrlGenerator;

use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Metadata\AdminResource;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\Create;
use LAG\AdminBundle\Metadata\Operation;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Resource\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Routing\Parameter\ParametersMapper;
use LAG\AdminBundle\Routing\Route\RouteNameGeneratorInterface;
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
        $inflector = new EnglishInflector();
        $path = u('/')
            ->append($inflector->pluralize($resource->getName())[0])
        ;

        if ($operation instanceof CollectionOperationInterface) {
            return $path->toString();
        }

        if (!$operation instanceof Create) {
            foreach ($resource->getIdentifiers() as $identifier) {
                $path = $path
                    ->append('/{')
                    ->append($identifier)
                    ->append('}')
                ;
            }
        }

        return $path
            ->append('/')
            ->append($operation->getName())
            ->lower()
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

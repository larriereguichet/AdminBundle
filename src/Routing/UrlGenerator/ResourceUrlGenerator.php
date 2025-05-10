<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Routing\UrlGenerator;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Metadata\Url;
use LAG\AdminBundle\Resource\Factory\OperationFactoryInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

final readonly class ResourceUrlGenerator implements ResourceUrlGeneratorInterface
{
    public function __construct(
        private RouterInterface $router,
        private ParametersMapperInterface $mapper,
        private OperationFactoryInterface $operationFactory,
    ) {
    }

    public function generate(OperationInterface $operation, mixed $data = null, int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): string
    {
        $parameters = $this->mapper->map($data, $operation->getRouteParameters());

        if (\count($parameters) !== \count($operation->getRouteParameters())) {
            throw new Exception(\sprintf('Unable to generate URL for resource "%s" and operation "%s". Expected "%s" route parameters, got "%s"', $operation->getResource()->getName(), $operation->getName(), \count($operation->getRouteParameters()), \count($parameters)));
        }

        return $this->router->generate($operation->getRoute(), $parameters, $referenceType);
    }

    public function generateFromUrl(Url $url, mixed $data = null, int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): string
    {
        if ($url->getUrl() !== null) {
            return $url->getUrl();
        }

        if ($url->getOperation() !== null) {
            return $this->generateFromOperationName($url->getOperation(), $data, $referenceType);
        }

        if ($url->getRoute() !== null) {
            return $this->generateFromRouteName(
                $url->getRoute(),
                $url->getRouteParameters(),
                $data,
                $referenceType,
            );
        }

        throw new Exception('Unable to generate a route for an action');
    }

    public function generateFromRouteName(
        string $routeName,
        array $routeParameters = [],
        mixed $data = null,
        int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH,
    ): string {
        $mappedRouteParameters = $routeParameters;

        if ($data !== null) {
            $mappedRouteParameters = new ParametersMapper()->map($data, $routeParameters);
        }

        return $this->router->generate($routeName, $mappedRouteParameters, $referenceType);
    }

    public function generateFromOperationName(
        string $operationName,
        mixed $data = null,
        int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH,
    ): string {
        $operation = $this->operationFactory->create($operationName);

        return $this->generate($operation, $data, $referenceType);
    }
}

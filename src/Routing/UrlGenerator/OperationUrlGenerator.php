<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Routing\UrlGenerator;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Routing\Mapper\ParametersMapperInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

final readonly class OperationUrlGenerator implements OperationUrlGeneratorInterface
{
    public function __construct(
        private RouterInterface $router,
        private ParametersMapperInterface $mapper,
    ) {
    }

    public function generateUrl(OperationInterface $operation, mixed $data = null, int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): string
    {
        $parameters = $this->mapper->mapObjectToRouteParameters($data, $operation->getRouteParameters());

        if (\count($parameters) !== \count($operation->getRouteParameters())) {
            throw new Exception(\sprintf('Unable to generate URL for resource "%s" and operation "%s". Expected "%s" route parameters, got "%s"', $operation->getResource()->getShortName(), $operation->getName(), \count($operation->getRouteParameters()), \count($parameters)));
        }

        return $this->router->generate($operation->getRoute(), $parameters, $referenceType);
    }
}

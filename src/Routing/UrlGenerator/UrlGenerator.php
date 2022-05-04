<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Routing\UrlGenerator;

use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Routing\Parameter\ParametersMapper;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Routing\RouterInterface;

class UrlGenerator implements UrlGeneratorInterface
{
    public function __construct(
        private RouterInterface $router,
        private ApplicationConfiguration $applicationConfiguration,
    ) {
    }

    public function generate(
        string $adminName,
        string $actionName,
        array $routeParameters = [],
        object $data = null
    ): string {
        $routeName = $this->applicationConfiguration->getRouteName($adminName, $actionName);

        return $this->generateFromRouteName($routeName, $routeParameters, $data);
    }

    public function generateFromRouteName(string $routeName, array $routeParameters = [], object $data = null): string
    {
        $mappedRouteParameters = $routeParameters;

        if ($data !== null) {
            $mapper = new ParametersMapper();
            $mappedRouteParameters = $mapper->map($data, $routeParameters);
        }

        return $this->router->generate($routeName, $mappedRouteParameters);
    }
}

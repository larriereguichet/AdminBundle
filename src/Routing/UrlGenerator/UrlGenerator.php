<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Routing\UrlGenerator;

use LAG\AdminBundle\Admin\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Routing\Parameter\ParametersMapper;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Routing\RouterInterface;

class UrlGenerator implements UrlGeneratorInterface
{
    private RouterInterface $router;
    private ApplicationConfiguration $appConfig;

    public function __construct(RouterInterface $router, ApplicationConfiguration $appConfig)
    {
        $this->router = $router;
        $this->appConfig = $appConfig;
    }

    public function generate(
        string $adminName,
        string $actionName,
        array $routeParameters = [],
        object $data = null
    ): string {
        $routeName = $this->appConfig->getRouteName($adminName, $actionName);

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

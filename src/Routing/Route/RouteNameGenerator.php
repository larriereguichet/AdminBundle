<?php

namespace LAG\AdminBundle\Routing\Route;

use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;

class RouteNameGenerator implements RouteNameGeneratorInterface
{
    public function __construct(private ApplicationConfiguration $applicationConfiguration)
    {
    }

    public function generateRouteName(string $adminName, string $actionName): string
    {
        return $this->applicationConfiguration->getRouteName($adminName, $actionName);
    }
}

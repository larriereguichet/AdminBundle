<?php

namespace LAG\AdminBundle\Routing;

use Exception;
use LAG\AdminBundle\Admin\Configuration\AdminConfiguration;

class RouteNameGenerator
{
    public function generate($actionName, $adminName, AdminConfiguration $configuration)
    {
        if (!array_key_exists($actionName, $configuration->getParameter('actions'))) {
            throw new Exception(
                sprintf('Invalid action name %s for admin %s (available action are: %s)',
                    $actionName,
                    $adminName,
                    implode(', ', array_keys($configuration->getParameter('actions'))))
            );
        }
        // generate the route name using the configured pattern
        $routeName = str_replace(
            '{admin}',
            strtolower($adminName),
            $configuration->getParameter('routing_name_pattern')
        );
        $routeName = str_replace(
            '{action}',
            $actionName,
            $routeName
        );
    
        return $routeName;
    }
}

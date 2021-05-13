<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Factory\Configuration;

use LAG\AdminBundle\Configuration\ActionConfiguration;
use LAG\AdminBundle\Exception\ConfigurationException;

interface ActionConfigurationFactoryInterface
{
    /**
     * Create a new action configuration object from the given parameter.
     *
     * @throws ConfigurationException
     */
    public function create(string $actionName, array $options = []): ActionConfiguration;
}

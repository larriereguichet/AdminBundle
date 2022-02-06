<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Factory\Configuration;

use LAG\AdminBundle\Configuration\ActionConfiguration;
use LAG\AdminBundle\Configuration\AdminConfiguration;
use LAG\AdminBundle\Exception\ConfigurationException;

interface ConfigurationFactoryInterface
{
    /**
     * Create a new admin configuration object from with the given options.
     *
     * @throws ConfigurationException
     */
    public function createAdminConfiguration(string $adminName, array $options = []): AdminConfiguration;

    /**
     * Create a new action configuration object from the given options.
     */
    public function createActionConfiguration(string $actionName, array $options = []): ActionConfiguration;
}

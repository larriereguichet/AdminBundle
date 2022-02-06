<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Factory\Configuration;

use LAG\AdminBundle\Configuration\ActionConfiguration;
use LAG\AdminBundle\Configuration\AdminConfiguration;

class ConfigurationFactory implements ConfigurationFactoryInterface
{
    public function __construct(
        private AdminConfigurationFactoryInterface $adminConfigurationFactory,
        private ActionConfigurationFactoryInterface $actionConfigurationFactory,
    ) {
    }

    public function createAdminConfiguration(string $adminName, array $options = []): AdminConfiguration
    {
        return $this->adminConfigurationFactory->create($adminName, $options);
    }

    public function createActionConfiguration(string $actionName, array $options = []): ActionConfiguration
    {
        return $this->actionConfigurationFactory->create($actionName, $options);
    }
}

<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Factory\Configuration;

use LAG\AdminBundle\Admin\Configuration\ActionConfiguration;
use LAG\AdminBundle\Admin\Configuration\AdminConfiguration;

class CachedConfigurationFactory extends ConfigurationFactory
{
    private array $adminCache = [];

    public function createAdminConfiguration(string $adminName, array $options = []): AdminConfiguration
    {
        if (\array_key_exists($adminName, $this->adminCache)) {
            return $this->adminCache[$adminName];
        }
        $configuration = parent::createAdminConfiguration($adminName, $options);
        $this->adminCache[$adminName] = $configuration;

        return $configuration;
    }

    public function createActionConfiguration(string $actionName, array $options = []): ActionConfiguration
    {
        return parent::createActionConfiguration($actionName, $options);
    }
}

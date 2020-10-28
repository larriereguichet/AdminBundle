<?php

namespace LAG\AdminBundle\Factory\Configuration;

use LAG\AdminBundle\Configuration\ActionConfiguration;
use LAG\AdminBundle\Configuration\AdminConfiguration;
use LAG\AdminBundle\Configuration\MenuConfiguration;

class ConfigurationFactory implements ConfigurationFactoryInterface
{
    private AdminConfigurationFactoryInterface $adminConfigurationFactory;
    private ActionConfigurationFactoryInterface $actionConfigurationFactory;
    private MenuConfigurationFactoryInterface $menuConfigurationFactory;

    public function __construct(
        AdminConfigurationFactoryInterface $adminConfigurationFactory,
        ActionConfigurationFactoryInterface $actionConfigurationFactory,
        MenuConfigurationFactoryInterface $menuConfigurationFactory
    ) {
        $this->adminConfigurationFactory = $adminConfigurationFactory;
        $this->actionConfigurationFactory = $actionConfigurationFactory;
        $this->menuConfigurationFactory = $menuConfigurationFactory;
    }

    public function createAdminConfiguration(string $adminName, array $options = []): AdminConfiguration
    {
        return $this->adminConfigurationFactory->create($adminName, $options);
    }

    public function createActionConfiguration(string $actionName, array $options = []): ActionConfiguration
    {
        return $this->actionConfigurationFactory->create($actionName, $options);
    }

    public function createMenuConfiguration(string $menuName, array $options = []): MenuConfiguration
    {
        return $this->menuConfigurationFactory->create($menuName, $options);
    }
}

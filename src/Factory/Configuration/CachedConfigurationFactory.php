<?php

namespace LAG\AdminBundle\Factory\Configuration;

use LAG\AdminBundle\Configuration\ActionConfiguration;
use LAG\AdminBundle\Configuration\AdminConfiguration;
use LAG\AdminBundle\Configuration\MenuConfiguration;

class CachedConfigurationFactory extends ConfigurationFactory
{
    private array $adminCache = [];
    private array $menuCache = [];

//    public function createAdminConfiguration(string $adminName, array $options = []): AdminConfiguration
//    {
//        if (\array_key_exists($adminName, $this->adminCache)) {
//            return $this->adminCache[$adminName];
//        }
//        $configuration = parent::createAdminConfiguration($adminName, $options);
//        $this->adminCache[$adminName] = $configuration;
//
//        return $configuration;
//    }
//
//    public function createActionConfiguration(string $actionName, array $options = []): ActionConfiguration
//    {
//        return parent::createActionConfiguration($actionName, $options);
//    }
//
//    public function createMenuConfiguration(string $menuName, array $options = []): MenuConfiguration
//    {
//        if (\array_key_exists($menuName, $this->menuCache)) {
//            return $this->menuCache[$menuName];
//        }
//        $configuration = parent::createMenuConfiguration($menuName, $options);
//        $this->menuCache[$menuName] = $configuration;
//
//        return $configuration;
//    }
}

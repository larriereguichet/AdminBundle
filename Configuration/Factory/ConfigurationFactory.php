<?php

namespace LAG\AdminBundle\Configuration\Factory;

use LAG\AdminBundle\Action\Configuration\ActionConfiguration;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Admin\Configuration\AdminConfiguration;
use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfigurationFactory
{
    /**
     * @var ApplicationConfiguration
     */
    protected $applicationConfiguration;

    /**
     * Create an action configuration object.
     *
     * @param $actionName
     * @param AdminInterface $admin
     * @param array $configuration
     * @return ActionConfiguration
     */
    public function createActionConfiguration($actionName, AdminInterface $admin, array $configuration = [])
    {
        $resolver = new OptionsResolver();
        $actionConfiguration = new ActionConfiguration($actionName, $admin);
        $actionConfiguration->configureOptions($resolver);
        $actionConfiguration->setParameters($resolver->resolve($configuration));

        return $actionConfiguration;
    }

    /**
     * Create an application configuration object.
     *
     * @param array $configuration
     * @return ApplicationConfiguration
     */
    public function createApplicationConfiguration(array $configuration = [])
    {
        $resolver = new OptionsResolver();
        $this->applicationConfiguration = new ApplicationConfiguration();
        $this->applicationConfiguration->configureOptions($resolver);
        $this->applicationConfiguration->setParameters($resolver->resolve($configuration));

        return $this->applicationConfiguration;
    }

    /**
     * Create an admin configuration object.
     *
     * @param array $configuration
     * @return AdminConfiguration
     */
    public function createAdminConfiguration(array $configuration = [])
    {
        $resolver = new OptionsResolver();
        $adminConfiguration = new AdminConfiguration($this->applicationConfiguration);
        $adminConfiguration->configureOptions($resolver);
        $adminConfiguration->setParameters($resolver->resolve($configuration));

        return $adminConfiguration;
    }

    /**
     * @return ApplicationConfiguration
     */
    public function getApplicationConfiguration()
    {
        return $this->applicationConfiguration;
    }
}

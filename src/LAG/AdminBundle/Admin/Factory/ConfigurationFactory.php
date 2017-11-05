<?php

namespace LAG\AdminBundle\Admin\Factory;

use LAG\AdminBundle\Admin\Configuration\AdminConfiguration;
use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Application\Configuration\ApplicationConfigurationStorage;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Handle the creation of the admin configuration objects.
 */
class ConfigurationFactory
{
    /**
     * @var ApplicationConfiguration
     */
    private $applicationConfiguration;
    
    /**
     * ConfigurationFactory constructor.
     *
     * @param ApplicationConfigurationStorage $storage
     */
    public function __construct(ApplicationConfigurationStorage $storage)
    {
        $this->applicationConfiguration = $storage->getApplicationConfiguration();
    }
    
    /**
     * Create an admin configuration object, from the given configuration array and the configured application
     * configuration.
     *
     * @param array $configuration
     *
     * @return AdminConfiguration
     */
    public function create(array $configuration = [])
    {
        $resolver = new OptionsResolver();
        $adminConfiguration = new AdminConfiguration($this->applicationConfiguration);
        $adminConfiguration->configureOptions($resolver);
        $adminConfiguration->setParameters($resolver->resolve($configuration));
    
        return $adminConfiguration;
    }
}

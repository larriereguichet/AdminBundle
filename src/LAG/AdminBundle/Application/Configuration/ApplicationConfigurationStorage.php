<?php

namespace LAG\AdminBundle\Application\Configuration;

use Exception;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Store the application configuration object to be used in other parts of the application.
 */
class ApplicationConfigurationStorage
{
    /**
     * @var ApplicationConfiguration
     */
    private $configuration;
    
    /**
     * @var bool
     */
    private $frozen = false;
    
    /**
     * ApplicationConfigurationStorage constructor.
     *
     * Define the application configuration. Once the configuration is defined, it can not be defined anymore. An
     * exception will be thrown if the set method is called a second time.
     *
     * @param array $configuration
     */
    public function __construct(array $configuration = [])
    {
        // resolve the application configuration array in to a configuration object
        $resolver = new OptionsResolver();
        $applicationConfiguration = new ApplicationConfiguration();
        $applicationConfiguration->configureOptions($resolver);
        $applicationConfiguration->setParameters($resolver->resolve($configuration));
    
        $this->configuration = $applicationConfiguration;
        $this->frozen = true;
    }
    
    /**
     * Return the application configuration. If the none is defined yet, an exception will be thrown.
     *
     * @return ApplicationConfiguration
     *
     * @throws Exception
     */
    public function getApplicationConfiguration()
    {
        if (null === $this->configuration) {
            throw new Exception('The application configuration has not been set');
        }
        
        return $this->configuration;
    }
    
    /**
     * Return true is the configuration is defined and the storage frozen.
     *
     * @return bool
     */
    public function isFrozen()
    {
        return $this->frozen;
    }
}

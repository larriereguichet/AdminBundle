<?php

namespace LAG\AdminBundle\Configuration;

use LAG\AdminBundle\Exception\Exception;
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
     * Return the application configuration. If the none is defined yet, an exception will be thrown.
     *
     * @return ApplicationConfiguration
     *
     * @throws Exception
     */
    public function getConfiguration()
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

    /**
     * @param array $configuration
     *
     * @throws Exception
     */
    public function setConfiguration(array $configuration)
    {
        if ($this->frozen) {
            throw new Exception('The application configuration is already defined');
        }
        dump($configuration);
        $this->configuration = $this->createApplicationConfiguration($configuration);
        $this->frozen = true;
    }

    /**
     * @param array $configuration
     *
     * @return ApplicationConfiguration
     *
     * @throws Exception
     */
    private function createApplicationConfiguration(array $configuration)
    {
        $resolver = new OptionsResolver();
        $applicationConfiguration = new ApplicationConfiguration();
        $applicationConfiguration->configureOptions($resolver);

        try {
            //dump($resolver->resolve($configuration));
            $applicationConfiguration->setParameters($resolver->resolve($configuration));
        } catch (Exception $exception) {
            throw new Exception(
                'An error has been found in the admin application configuration',
                0,
                $exception
            );
        }

        return $applicationConfiguration;
    }
}

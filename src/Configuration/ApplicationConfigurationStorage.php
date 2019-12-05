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
     * @throws Exception
     */
    public function getConfiguration(): ApplicationConfiguration
    {
        if (null === $this->configuration) {
            throw new Exception('The application configuration has not been set. Try to clear the cache (bin/console ca:cl)');
        }

        return $this->configuration;
    }

    /**
     * Return true is the configuration is defined and the storage frozen.
     */
    public function isFrozen(): bool
    {
        return $this->frozen;
    }

    /**
     * @throws Exception
     */
    public function setConfiguration(array $configuration): void
    {
        if ($this->frozen) {
            throw new Exception('The application configuration is already defined');
        }
        $this->configuration = $this->createApplicationConfiguration($configuration);
        $this->frozen = true;
    }

    /**
     * @throws Exception
     */
    private function createApplicationConfiguration(array $configuration): ApplicationConfiguration
    {
        $resolver = new OptionsResolver();
        $applicationConfiguration = new ApplicationConfiguration();
        $applicationConfiguration->configureOptions($resolver);

        try {
            $applicationConfiguration->setParameters($resolver->resolve($configuration));
        } catch (Exception $exception) {
            throw new Exception('An error has been found when processing the configuration of the admin application', 0, $exception);
        }

        return $applicationConfiguration;
    }
}

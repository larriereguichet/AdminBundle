<?php

namespace LAG\AdminBundle\Application\Configuration;

interface ApplicationConfigurationAwareInterface
{
    /**
     * Define the application configuration.
     *
     * @param ApplicationConfiguration $configuration
     */
    public function setApplicationConfiguration(ApplicationConfiguration $configuration);
}

<?php

namespace LAG\AdminBundle\Field\Configuration;

use JK\Configuration\Configuration;
use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Application\Configuration\ApplicationConfigurationAwareInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StringFieldConfiguration extends Configuration implements ApplicationConfigurationAwareInterface
{
    /**
     * @var ApplicationConfiguration
     */
    protected $applicationConfiguration;
    
    /**
     * Configure options resolver.
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        
        $resolver->setDefaults([
            'length' => $this->applicationConfiguration->getParameter('string_length'),
            'replace' => $this->applicationConfiguration->getParameter('string_length_truncate'),
            'translation' => true,
        ]);
    }
    
    /**
     * Define the application configuration.
     *
     * @param ApplicationConfiguration $configuration
     */
    public function setApplicationConfiguration(ApplicationConfiguration $configuration)
    {
        $this->applicationConfiguration = $configuration;
    }
}

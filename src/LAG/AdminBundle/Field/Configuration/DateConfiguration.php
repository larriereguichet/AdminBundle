<?php

namespace LAG\AdminBundle\Field\Configuration;

use JK\Configuration\Configuration;
use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Application\Configuration\ApplicationConfigurationAwareInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Date field.
 */
class DateConfiguration extends Configuration implements ApplicationConfigurationAwareInterface
{
    /**
     * @var ApplicationConfiguration
     */
    private $applicationConfiguration;
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'format' => $this
                ->applicationConfiguration
                ->getParameter('date_format'),
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

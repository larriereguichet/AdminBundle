<?php

namespace LAG\AdminBundle\Field\Factory;

use Exception;
use JK\Configuration\Configuration;
use LAG\AdminBundle\Application\Configuration\ApplicationConfigurationAwareInterface;
use LAG\AdminBundle\Application\Configuration\ApplicationConfigurationStorage;
use LAG\AdminBundle\Field\FieldInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfigurationFactory
{
    /**
     * @var ApplicationConfigurationStorage
     */
    private $applicationConfigurationStorage;
    
    /**
     * ConfigurationFactory constructor.
     *
     * @param ApplicationConfigurationStorage $applicationConfigurationStorage
     */
    public function __construct(ApplicationConfigurationStorage $applicationConfigurationStorage)
    {
        $this->applicationConfigurationStorage = $applicationConfigurationStorage;
    }
    
    /**
     * Create a new field Configuration object.
     *
     * @param FieldInterface $field
     * @param array          $configuration
     *
     * @return Configuration
     *
     * @throws Exception
     */
    public function create(FieldInterface $field, array $configuration = [])
    {
        $resolver = new OptionsResolver();
    
        $class = $field->getConfigurationClass();
        $fieldConfiguration = new $class();
    
        if (!$fieldConfiguration instanceof Configuration) {
            throw new Exception('The field configuration should be an instance of '.Configuration::class);
        }
    
        if ($fieldConfiguration instanceof ApplicationConfigurationAwareInterface) {
            $fieldConfiguration->setApplicationConfiguration(
                $this
                    ->applicationConfigurationStorage
                    ->getApplicationConfiguration()
            );
        }
        $fieldConfiguration->configureOptions($resolver);
        $fieldConfiguration->setParameters($resolver->resolve($configuration));
    
        return $fieldConfiguration;
    }
}

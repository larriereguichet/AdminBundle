<?php

namespace LAG\AdminBundle\Field\Factory;

use Exception;
use JK\Configuration\Configuration;
use LAG\AdminBundle\Application\Configuration\ApplicationConfigurationAwareInterface;
use LAG\AdminBundle\Application\Configuration\ApplicationConfigurationStorage;
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
     * Create an instance of a configuration object from a field configuration.
     *
     * @param array $configuration
     *
     * @return Configuration
     *
     * @throws Exception
     */
    public function create(array $configuration)
    {
        $mapping = $this
            ->applicationConfigurationStorage
            ->getApplicationConfiguration()
            ->getParameter('fields_mapping');

        // At this point, the "type" key should be defined
        if (!array_key_exists('type', $configuration)) {
            throw new Exception(
                'Invalid configuration : missing key "type"'
            );
        }
        $class = $configuration['type'];

        // The given type should be defined in the fields mapping
        if (!array_key_exists($class, $mapping)) {
            throw new Exception(
                'Invalid field class "' . $class . '" Available field classes are ' . implode(',', array_keys($mapping))
            );
        }
        $configurationClass = $mapping[$class];
        $configurationObject = new $configurationClass();

        // As the configuration class is dynamic, we should check if it is a correct configuration class
        if (!$configurationObject instanceof Configuration) {
            throw new Exception('The field configuration should be an instance of ' . Configuration::class);
        }

        // Some configuration can inherit configuration
        if ($configurationObject instanceof ApplicationConfigurationAwareInterface) {
            $configurationObject->setApplicationConfiguration(
                $this
                    ->applicationConfigurationStorage
                    ->getApplicationConfiguration()
            );
        }
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'sortable' => true,
            'column_class' => '',
        ]);
        $configurationObject->configureOptions($resolver);
        $configurationObject->setParameters($resolver->resolve($configuration));

        return $configurationObject;
    }
}

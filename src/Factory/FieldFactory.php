<?php

namespace LAG\AdminBundle\Factory;

use Exception;
use LAG\AdminBundle\Admin\Exception\AdminException;
use LAG\AdminBundle\Configuration\ActionConfiguration;
use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Configuration\ApplicationConfigurationStorage;
use LAG\AdminBundle\Field\FieldInterface;
use LAG\AdminBundle\Field\TwigAwareFieldInterface;
use LAG\AdminBundle\Field\TwigAwareInterface;
use LAG\AdminBundle\Field\TranslatorAwareFieldInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;
use Twig_Environment;

/**
 * Field factory. Instances fields.
 */
class FieldFactory
{
    /**
     * Application configuration
     *
     * @var ApplicationConfiguration
     */
    protected $configuration;

    /**
     * Field class mapping array, indexed by field type.
     *
     * @var array
     */
    protected $fieldsMapping = [];

    /**
     * Translator for field values.
     *
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * Twig engine.
     *
     * @var Twig_Environment
     */
    protected $twig;
    
    /**
     * @var ConfigurationFactory
     */
    protected $configurationFactory;
    
    /**
     * FieldFactory constructor.
     *
     * @param ApplicationConfigurationStorage                     $applicationConfigurationStorage
     * @param ConfigurationFactory $configurationFactory
     * @param TranslatorInterface                                 $translator
     * @param Twig_Environment                                    $twig
     */
    public function __construct(
        ApplicationConfigurationStorage $applicationConfigurationStorage,
        ConfigurationFactory $configurationFactory,
        TranslatorInterface $translator,
        Twig_Environment $twig
    ) {
        $this->configuration = $applicationConfigurationStorage->getConfiguration();
        $this->fieldsMapping = $this
            ->configuration
            ->getParameter('fields_mapping'); // shortcut to field mapping array
        $this->translator = $translator;
        $this->twig = $twig;
        $this->configurationFactory = $configurationFactory;
    }
    
    /**
     * @param ActionConfiguration $configuration
     *
     * @return array
     */
    public function getFields(ActionConfiguration $configuration)
    {
        $fields = [];
    
        foreach ($configuration->getParameter('fields') as $field => $fieldConfiguration) {
            $fields[] = $this->create($field, $fieldConfiguration, $configuration);
        }
    
        return $fields;
    }
    
    
    /**
     * Create a new field instance according to the given configuration.
     *
     * @param string              $name
     * @param array               $configuration
     * @param ActionConfiguration $actionConfiguration
     *
     * @return FieldInterface
     *
     * @throws Exception
     */
    public function create($name, array $configuration = [], ActionConfiguration $actionConfiguration)
    {
        $resolver = new OptionsResolver();
        $configuration = $this->resolveTopLevelConfiguration($configuration, $actionConfiguration);

        $field = $this->instanciateField($name, $configuration['type']);
        $field->configureOptions($resolver, $actionConfiguration);

        try {
            $field->setOptions($resolver->resolve($configuration['options']));
        } catch (Exception $exception) {
            throw new \LAG\AdminBundle\Exception\Exception(
                'An error has occurred when resolving the options for the field "'.$name.'"',
                $exception->getCode(),
                $exception
            );
        }

        return $field;
    }

    /**
     * Return field class according to the field type. If the type is not present in the field mapping array, an
     * exception will be thrown.
     *
     * @param $type
     * @return string
     * @throws Exception
     */
    private function getFieldClass($type)
    {
        if (!array_key_exists($type, $this->fieldsMapping)) {
            throw new Exception("Field type {$type} not found in field mapping. Check your configuration");
        }

        return $this->fieldsMapping[$type];
    }
    
    /**
     * @param $name
     * @param $type
     *
     * @return FieldInterface
     *
     * @throws Exception
     */
    private function instanciateField($name, $type)
    {
        $fieldClass = $this->getFieldClass($type);
        $field = new $fieldClass($name);
    
        if (!$field instanceof FieldInterface) {
            throw new Exception("Field class {$fieldClass} must implements ".FieldInterface::class);
        }
    
        if ($field instanceof TranslatorAwareFieldInterface) {
            $field->setTranslator($this->translator);
        }
        if ($field instanceof TwigAwareFieldInterface) {
            $field->setTwig($this->twig);
        }
    
        return $field;
    }

    /**
     * @param array               $configuration
     * @param ActionConfiguration $actionConfiguration
     * @return array
     * @throws Exception
     */
    private function resolveTopLevelConfiguration(array $configuration, ActionConfiguration $actionConfiguration)
    {
        $resolver = new OptionsResolver();
        $resolver
            ->setDefaults([
                'type' => 'string',
                'options' => [],
            ])
            // Set allowed fields type from tagged services
            ->setAllowedValues('type', array_keys($this->fieldsMapping))
            ->setAllowedTypes('type', 'string')
            ->setAllowedTypes('options', 'array')
        ;
        $configuration = $resolver->resolve($configuration);
    
        // For collection of fields, we resolve the configuration of each item
        if ($configuration['type'] == 'collection') {
            $items = [];
        
            foreach ($configuration['options'] as $itemFieldName => $itemFieldConfiguration) {
                // The configuration should be an array
                if (!$itemFieldConfiguration) {
                    $itemFieldConfiguration = [];
                }
                
                // The type should be defined
                if (!array_key_exists('type', $itemFieldConfiguration)) {
                    throw new Exception("Missing type configuration for field {$itemFieldName}");
                }
    
                // The field options are optional
                if (!array_key_exists('options', $itemFieldConfiguration)) {
                    $itemFieldConfiguration['options'] = [];
                }
                
                // create collection item
                $items[] = $this->create($itemFieldName, $itemFieldConfiguration, $actionConfiguration);
            }
            // add created item to the field options
            $configuration['options'] = [
                'fields' => $items,
            ];
        }
    
        return $configuration;
    }
}
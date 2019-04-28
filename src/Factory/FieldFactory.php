<?php

namespace LAG\AdminBundle\Factory;

use Exception;
use LAG\AdminBundle\Configuration\ActionConfiguration;
use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Configuration\ApplicationConfigurationStorage;
use LAG\AdminBundle\Configuration\FieldConfiguration;
use LAG\AdminBundle\Field\FieldInterface;
use LAG\AdminBundle\Field\TwigAwareFieldInterface;
use LAG\AdminBundle\Field\TranslatorAwareFieldInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * Field factory. Instances fields.
 */
class FieldFactory
{
    /**
     * Application configuration.
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
     * @var Environment
     */
    protected $twig;

    /**
     * @var ConfigurationFactory
     */
    protected $configurationFactory;

    /**
     * FieldFactory constructor.
     *
     * @param ApplicationConfigurationStorage $applicationConfigurationStorage
     * @param ConfigurationFactory            $configurationFactory
     * @param TranslatorInterface             $translator
     * @param Environment                $twig
     */
    public function __construct(
        ApplicationConfigurationStorage $applicationConfigurationStorage,
        ConfigurationFactory $configurationFactory,
        TranslatorInterface $translator,
        Environment $twig
    ) {
        $this->configuration = $applicationConfigurationStorage->getConfiguration();
        $this->fieldsMapping = $this
            ->configuration
            ->getParameter('fields_mapping'); // shortcut to the fields mapping array
        $this->translator = $translator;
        $this->twig = $twig;
        $this->configurationFactory = $configurationFactory;
    }

    /**
     * @param ActionConfiguration $configuration
     *
     * @return array
     */
    public function createFields(ActionConfiguration $configuration): array
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
    public function create(string $name, array $configuration, ActionConfiguration $actionConfiguration): FieldInterface
    {
        $resolver = new OptionsResolver();
        $configuration = $this->resolveConfiguration($configuration, $actionConfiguration);

        $field = $this->instanciateField($name, $configuration['type']);
        $field->configureOptions($resolver, $actionConfiguration);

        try {
            $field->setOptions($resolver->resolve($configuration['options']));
        } catch (Exception $exception) {
            throw new \LAG\AdminBundle\Exception\Exception(
                'An error has occurred when resolving the options for the field "'.$name.'": '.$exception->getMessage(),
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
     * @param string $type
     *
     * @return string
     *
     * @throws Exception
     */
    private function getFieldClass(string $type): string
    {
        if (!array_key_exists($type, $this->fieldsMapping)) {
            throw new Exception("Field type {$type} not found in field mapping. Check your configuration");
        }

        return $this->fieldsMapping[$type];
    }

    /**
     * @param string $name
     * @param string $type
     *
     * @return FieldInterface
     *
     * @throws Exception
     */
    private function instanciateField(string $name, string $type): FieldInterface
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
     *
     * @return array
     *
     * @throws Exception
     */
    private function resolveConfiguration(array $configuration, ActionConfiguration $actionConfiguration)
    {
        $resolver = new OptionsResolver();
        $fieldConfiguration = new FieldConfiguration(array_keys($this->fieldsMapping));
        $fieldConfiguration->configureOptions($resolver);
        $fieldConfiguration->setParameters($resolver->resolve($configuration));
        $configuration = $fieldConfiguration->all();

        // For collection of fields, we resolve the configuration of each item
        if ('collection' == $fieldConfiguration->getType()) {
            $items = [];

            foreach ($fieldConfiguration->getOptions() as $itemFieldName => $itemFieldConfiguration) {
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

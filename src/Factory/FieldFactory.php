<?php

namespace LAG\AdminBundle\Factory;

use LAG\AdminBundle\Configuration\ActionConfiguration;
use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Configuration\ApplicationConfigurationStorage;
use LAG\AdminBundle\Configuration\FieldConfiguration;
use LAG\AdminBundle\Event\Events;
use LAG\AdminBundle\Event\Events\FieldEvent;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Exception\Field\FieldTypeNotFoundException;
use LAG\AdminBundle\Field\FieldInterface;
use LAG\AdminBundle\Field\TranslatorAwareFieldInterface;
use LAG\AdminBundle\Field\TwigAwareFieldInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
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
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * FieldFactory constructor.
     */
    public function __construct(
        ApplicationConfigurationStorage $applicationConfigurationStorage,
        ConfigurationFactory $configurationFactory,
        TranslatorInterface $translator,
        EventDispatcherInterface $eventDispatcher,
        Environment $twig
    ) {
        $this->configuration = $applicationConfigurationStorage->getConfiguration();
        $this->fieldsMapping = $this
            ->configuration
            ->getParameter('fields_mapping'); // shortcut to the fields mapping array
        $this->translator = $translator;
        $this->twig = $twig;
        $this->configurationFactory = $configurationFactory;
        $this->eventDispatcher = $eventDispatcher;
    }

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
     * @throws Exception
     */
    public function create(string $name, array $configuration, ActionConfiguration $actionConfiguration): FieldInterface
    {
        $resolver = new OptionsResolver();
        $configuration = $this->resolveConfiguration($configuration, $actionConfiguration);

        // Dispatch an event to allow dynamic changes on the form type
        $event = new FieldEvent(
            $actionConfiguration->getAdminName(),
            $actionConfiguration->getActionName(),
            $name,
            $actionConfiguration->getAdminConfiguration()->get('entity'),
            $configuration['type']
        );
        $this->eventDispatcher->dispatch(Events::FIELD_PRE_CREATE, $event);

        if (null === $event->getType()) {
            throw new FieldTypeNotFoundException($event->getAdminName(), $event->getActionName(), $name);
        }
        $type = $event->getType();
        $options = array_merge($configuration['options'], $event->getOptions());

        if (!key_exists($type, $this->fieldsMapping)) {
            $type = 'auto';
        }
        $field = $this->instanciateField($name, $type);
        $field->configureOptions($resolver, $actionConfiguration);

        try {
            $field->setOptions($resolver->resolve($options));
        } catch (\Exception $exception) {
            throw new Exception('An error has occurred when resolving the options for the field "'.$name.'": '.$exception->getMessage(), $exception->getCode(), $exception);
        }
        $event = new FieldEvent(
            $actionConfiguration->getAdminName(),
            $actionConfiguration->getActionName(),
            $name,
            $actionConfiguration->getAdminConfiguration()->get('entity'),
            $type,
            $field
        );
        $this->eventDispatcher->dispatch(Events::FIELD_POST_CREATE, $event);

        return $field;
    }

    /**
     * Return field class according to the field type. If the type is not present in the field mapping array, an
     * exception will be thrown.
     *
     * @throws Exception
     */
    private function getFieldClass(string $type): string
    {
        if (!array_key_exists($type, $this->fieldsMapping)) {
            throw new Exception("Field type \"{$type}\" not found in field mapping. Allowed fields are \"".implode('", "', $this->fieldsMapping).'"');
        }

        return $this->fieldsMapping[$type];
    }

    /**
     * @throws Exception
     */
    private function instanciateField(string $name, string $type): FieldInterface
    {
        $fieldClass = $this->getFieldClass($type);
        $field = new $fieldClass($name);

        if (!$field instanceof FieldInterface) {
            throw new Exception("Field class \"{$fieldClass}\" must implements ".FieldInterface::class);
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

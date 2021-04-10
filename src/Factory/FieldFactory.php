<?php

namespace LAG\AdminBundle\Factory;

use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Configuration\FieldConfiguration;
use LAG\AdminBundle\Event\AdminEvents;
use LAG\AdminBundle\Event\Events\FieldDefinitionEvent;
use LAG\AdminBundle\Event\Events\FieldEvent;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Exception\Field\FieldConfigurationException;
use LAG\AdminBundle\Exception\Field\FieldTypeNotFoundException;
use LAG\AdminBundle\Field\ApplicationAwareInterface;
use LAG\AdminBundle\Field\FieldInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function Symfony\Component\String\u;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Field factory. Instances fields.
 */
class FieldFactory implements FieldFactoryInterface
{
    /**
     * Field class mapping array, indexed by field type.
     */
    protected array $fieldsMapping = [];
    protected EventDispatcherInterface $eventDispatcher;
    protected ApplicationConfiguration $appConfig;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        ApplicationConfiguration $appConfig,
        array $fieldsMapping
    ) {
        $this->fieldsMapping = $fieldsMapping;
        $this->eventDispatcher = $eventDispatcher;
        $this->appConfig = $appConfig;
    }

    public function create(string $name, array $configuration, array $context = []): FieldInterface
    {
        try {
            $configuration = $this->resolveConfiguration($name, $configuration, $context);
            // Dispatch an event to allow dynamic changes on the form type
            $event = new FieldEvent($name, $configuration['type'], $configuration['options'], $context);
            $this->eventDispatcher->dispatch($event, AdminEvents::FIELD_CREATE);
            $type = $event->getType();

            if ($type === null) {
                $type = 'auto';
            }
            $options = array_merge($configuration['options'], $event->getOptions());

            // Allow the type to be a class name
            if (!key_exists($type, $this->fieldsMapping) && !class_exists($type)) {
                throw new FieldTypeNotFoundException($type, $name, $context);
            }
            $field = $this->instanciateField($name, $type);

            if ($field instanceof ApplicationAwareInterface) {
                $field->setApplicationConfiguration($this->appConfig);
            }
            $this->configureField($field, $type, $options, $context);
        } catch (\Exception $exception) {
            throw new FieldConfigurationException($name, $context, $exception->getMessage(), $exception);
        }
        $event = new FieldEvent($name, $type, $options, $context);
        $this->eventDispatcher->dispatch($event, AdminEvents::FIELD_CREATED);

        return $field;
    }

    public function createDefinitions(string $class): array
    {
        $event = new FieldDefinitionEvent($class);
        $this->eventDispatcher->dispatch($event, AdminEvents::FIELD_DEFINITION_CREATE);

        return $event->getDefinitions();
    }

    private function configureField(
        FieldInterface $field,
        string $type,
        array $options,
        array $context
    ): void {
        $resolver = new OptionsResolver();
        $resolver
            ->setDefaults([
                'attr' => ['class' => 'admin-field admin-field-'.$type],
                'header_attr' => ['class' => 'admin-header admin-header-'.$type],
                'label' => null,
                'mapped' => false,
                'property_path' => $field->getName(),
                'template' => '@LAGAdmin/fields/auto.html.twig',
                'translation' => false, // Most of fields are values from database and should not be translated
                'translation_domain' => null,
                'sortable' => true,
            ])
            ->setAllowedTypes('attr', ['array', 'null'])
            ->setAllowedTypes('header_attr', ['array', 'null'])
            ->setAllowedTypes('label', ['string', 'null', 'boolean'])
            ->setAllowedTypes('mapped', ['boolean'])
            ->setAllowedTypes('property_path', ['string', 'null'])
            ->setAllowedTypes('template', ['string'])
            ->setAllowedTypes('translation', ['boolean'])
            ->setAllowedTypes('translation_domain', ['string', 'null'])
            ->setAllowedTypes('sortable', ['boolean'])
            ->setNormalizer('attr', function (Options $options, $attr) {
                if ($attr === null) {
                    $attr = [];
                }

                return $attr;
            })
            ->setNormalizer('header_attr', function (Options $options, $attr) {
                if ($attr === null) {
                    $attr = [];
                }

                return $attr;
            })
            ->setNormalizer('mapped', function (Options $options, $mapped) use ($field) {
                if (u($field->getName())->startsWith('_')) {
                    return true;
                }

                return $mapped;
            })
            ->setNormalizer('property_path', function (Options $options, $propertyPath) use ($field) {
                if (u($field->getName())->startsWith('_')) {
                    return null;
                }

                return $propertyPath;
            })
        ;

        if ($field->getParent()) {
            $currentField = $field;
            $parents = [];
            // Keep track of processed parent types to avoid a infinite loop
            $processedParents = [];

            while ($currentField->getParent() !== null) {
                if (in_array($currentField->getParent(), $processedParents)) {
                    throw new FieldConfigurationException($field->getName(), $context, 'An inheritance loop is found in '.implode(', ', $processedParents));
                }
                $parent = $this->instanciateField($currentField->getName(), $currentField->getParent());

                if ($parent instanceof ApplicationAwareInterface) {
                    $parent->setApplicationConfiguration($this->appConfig);
                }
                $parents[] = $parent;
                $processedParents[] = $field->getParent();
                $currentField = $parent;
            }
            $parents = array_reverse($parents);

            foreach ($parents as $parent) {
                $parent->configureOptions($resolver);
            }
        }
        $field->configureOptions($resolver);
        $field->setOptions($resolver->resolve($options));
    }

    /**
     * Return field class according to the field type. If the type is not present in the field mapping array, an
     * exception will be thrown.
     *
     * @throws Exception
     */
    private function getFieldClass(string $type): string
    {
        if (array_key_exists($type, $this->fieldsMapping)) {
            return $this->fieldsMapping[$type];
        }

        if (class_exists($type)) {
            return $type;
        }

        throw new Exception(sprintf('Field type "%s" not found in field mapping. Allowed fields are "%s"', $type, implode('", "', $this->fieldsMapping)));
    }

    private function instanciateField(string $name, string $type): FieldInterface
    {
        $fieldClass = $this->getFieldClass($type);
        $field = new $fieldClass($name, $type);

        if (!$field instanceof FieldInterface) {
            // TODO use an exception in the field namespace
            throw new Exception("Field class \"{$fieldClass}\" must implements ".FieldInterface::class);
        }

        return $field;
    }

    private function resolveConfiguration(string $name, array $configuration, array $context): array
    {
        $fieldConfiguration = new FieldConfiguration();
        $fieldConfiguration->configure($configuration);
        $configuration = $fieldConfiguration->toArray();

        $event = new FieldEvent($name, $configuration['type'], $configuration['options'], $context);
        $this->eventDispatcher->dispatch($event);
        $configuration['options'] = $event->getOptions();

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
                $items[] = $this->create($itemFieldName, $itemFieldConfiguration, $context);
            }
            // add created item to the field options
            $configuration['options'] = [
                'fields' => $items,
            ];
        }

        return $configuration;
    }
}

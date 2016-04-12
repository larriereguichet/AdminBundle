<?php

namespace LAG\AdminBundle\Field\Factory;

use Exception;
use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Field\Field;
use LAG\AdminBundle\Field\FieldInterface;
use LAG\AdminBundle\Field\TwigFieldInterface;
use LAG\AdminBundle\Field\TranslatableFieldInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;
use Twig_Environment;

/**
 * Field factory. Instances fields with its renderer.
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
     * FieldFactory constructor.
     *
     * @param ApplicationConfiguration $configuration
     * @param TranslatorInterface $translator
     * @param Twig_Environment $twig
     */
    public function __construct(
        ApplicationConfiguration $configuration,
        TranslatorInterface $translator,
        Twig_Environment $twig
    ) {
        $this->configuration = $configuration;
        $this->fieldsMapping = $configuration->getParameter('fields_mapping'); // shortcut to field mapping array
        $this->translator = $translator;
        $this->twig = $twig;
    }

    /**
     * Create a new field with its renderer.
     *
     * @param $fieldName
     * @param array $configuration
     *
     * @return Field
     *
     * @throws Exception
     */
    public function create($fieldName, array $configuration = [])
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'type' => 'string',
            'options' => [],
        ]);
        // set allowed fields type from tagged services
        $resolver->setAllowedValues('type', array_keys($this->fieldsMapping));
        $resolver->setAllowedTypes('type', 'string');
        $resolver->setAllowedTypes('options', 'array');

        // resolve options
        $configuration = $resolver->resolve($configuration);

        // for collection of fields, we resolve the configuration of each item
        if ($configuration['type'] == 'collection') {
            $items = [];

            foreach ($configuration['options'] as $itemFieldName => $itemFieldConfiguration) {

                // configuration should be an array
                if (!$itemFieldConfiguration) {
                    $itemFieldConfiguration = [];
                }
                // type should exists
                if (!array_key_exists('type', $configuration)) {
                    throw new Exception("Missing type configuration for field {$itemFieldName}");
                }
                // create collection item
                $items[] = $this->create($itemFieldName, $itemFieldConfiguration);
            }
            // add created item to the field options
            $configuration['options'] = [
                'fields' => $items,
            ];
        }
        // instanciate field
        $fieldClass = $this->getFieldMapping($configuration['type']);
        $field = new $fieldClass();

        if (!($field instanceof FieldInterface)) {
            throw new Exception("Field class {$fieldClass} must implements " . FieldInterface::class);
        }
        $field->setName($fieldName);
        $field->setApplicationConfiguration($this->configuration);

        if ($field instanceof TranslatableFieldInterface) {
            $field->setTranslator($this->translator);
        }
        if ($field instanceof TwigFieldInterface) {
            $field->setTwig($this->twig);
        }
        // clear revolver from previous default configuration
        $resolver->clear();

        // configure field default options
        $field->configureOptions($resolver);
        
        // resolve options
        $options = $resolver->resolve($configuration['options']);

        // set options
        $field->setOptions($options);
        
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
    public function getFieldMapping($type)
    {
        if (!array_key_exists($type, $this->fieldsMapping)) {
            throw new Exception("Field type {$type} not found in field mapping. Check your configuration");
        }

        return $this->fieldsMapping[$type];
    }
}

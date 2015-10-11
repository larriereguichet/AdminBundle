<?php

namespace BlueBear\AdminBundle\Admin\Factory;

use BlueBear\AdminBundle\Admin\Configuration\ApplicationConfiguration;
use BlueBear\AdminBundle\Admin\Field;
use BlueBear\AdminBundle\Admin\FieldInterface;
use BlueBear\BaseBundle\Behavior\ContainerTrait;
use Exception;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Field factory. Instances fields with its renderer
 */
class FieldFactory
{
    use ContainerTrait;

    protected $configuration;

    protected $fieldsMapping = [];

    public function __construct(ApplicationConfiguration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Create a new field with its renderer
     *
     * @param $fieldName
     * @param array $configuration
     * @return Field
     * @throws Exception
     */
    public function create($fieldName, array $configuration)
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'type' => 'string',
            'options' => []
        ]);
        // set allowed fields type from tagged services
        $resolver->setAllowedValues('type', array_keys($this->fieldsMapping));
        // resolve options
        $configuration = $resolver->resolve($configuration);
        // get field service name
        $fieldService = $this->fieldsMapping[$configuration['type']];
        // get field instance from container
        $field = $this
            ->container
            ->get($fieldService);

        if (!($field instanceof FieldInterface)) {
            throw new Exception(sprintf('Field "%s" should implements BlueBear\AdminBundle\Admin\FieldInterface', get_class($field)));
        }
        if ($field->getType() != $configuration['type']) {
            throw new Exception(sprintf('Field type mismatch for service "%s"', $fieldService));
        }
        // clear revolver from previous default configuration
        $resolver->clear();
        // configure field default options
        $field->configureOptions($resolver);
        // resolve options
        $options = $resolver->resolve($configuration['options']);
        // setting options and value
        $field->setOptions($options);
        $field->setName($fieldName);

        return $field;
    }

    /**
     * Add a service id to the allowed field mapping
     *
     * @param $fieldType
     * @param $service
     * @throws Exception
     */
    public function addFieldMapping($fieldType, $service)
    {
        if (array_key_exists($fieldType, $this->fieldsMapping)) {
            throw new Exception(sprintf('You have already one service for Field type "%s"', $fieldType));
        }
        $this->fieldsMapping[$fieldType] = $service;
    }
}

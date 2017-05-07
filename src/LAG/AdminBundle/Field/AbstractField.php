<?php

namespace LAG\AdminBundle\Field;

use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractField implements FieldInterface
{
    const TYPE_STRING = 'string';
    const TYPE_LINK = 'link';
    const TYPE_ARRAY = 'array';
    const TYPE_DATE = 'date';
    const TYPE_COUNT = 'count';
    const TYPE_ACTION = 'action';
    const TYPE_COLLECTION = 'collection';
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_MAPPED = 'mapped';
    const TYPE_ACTION_COLLECTION = 'action_collection';

    /**
     * Field's name
     *
     * @var string
     */
    protected $name;

    /**
     * Application configuration for default options
     *
     * @var ApplicationConfiguration
     */
    protected $applicationConfiguration;

    /**
     * Field's resolved options
     *
     * @var ParameterBag
     */
    protected $options;

    /**
     * Field constructor.
     */
    public function __construct()
    {
        $this->options = new ParameterBag();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Return application configuration.
     *
     * @return ApplicationConfiguration
     */
    public function getApplicationConfiguration()
    {
        return $this->applicationConfiguration;
    }

    /**
     * @param ApplicationConfiguration $configuration
     */
    public function setApplicationConfiguration($configuration)
    {
        $this->applicationConfiguration = $configuration;
    }

    /**
     * Set resolved options.
     *
     * @param array $options
     * @return void
     */
    public function setOptions(array $options)
    {
        $this->options = new ParameterBag($options);
    }
    
    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
    }
}

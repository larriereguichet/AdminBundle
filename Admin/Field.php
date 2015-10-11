<?php

namespace BlueBear\AdminBundle\Admin;

use BlueBear\AdminBundle\Admin\Configuration\ApplicationConfiguration;

abstract class Field implements FieldInterface
{
    const TYPE_STRING = 'string';
    const TYPE_LINK = 'link';
    const TYPE_ARRAY = 'array';
    const TYPE_DATE = 'date';
    const TYPE_COUNT = 'count';
    const TYPE_ACTION = 'action';

    /**
     * Name of the field
     *
     * @var string
     */
    protected $name;

    /**
     * @var ApplicationConfiguration
     */
    protected $configuration;

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
     * @return ApplicationConfiguration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * @param ApplicationConfiguration $configuration
     */
    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;
    }
}

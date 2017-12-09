<?php

namespace LAG\AdminBundle\Field;

use JK\Configuration\Configuration;

interface FieldInterface
{
    /**
     * Render value of the field.
     *
     * @param mixed $value Value to render
     *
     * @return mixed
     */
    public function render($value);
    
    /**
     * Return the Field's name.
     *
     * @return string
     */
    public function getName();

    /**
     * Return the Field's type.
     *
     * @return string
     */
    public function getType();
    
    /**
     * Return the value of a configuration parameter.
     *
     * @param string    $name
     *
     * @param mixed|null $default
     *
     * @return mixed
     */
    public function getConfiguration($name, $default = null);
    
    /**
     * Return the Field's configuration class.
     *
     * @return string
     */
    public function getConfigurationClass();
    
    /**
     * @param Configuration $configuration
     */
    public function setConfiguration(Configuration $configuration);
}

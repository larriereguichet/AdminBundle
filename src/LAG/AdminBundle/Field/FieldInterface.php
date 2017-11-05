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
     * Return a configuration parameter value.
     *
     * @param string $name The name of the parameter name
     *
     * @return mixed The configuration value
     */
    public function getConfiguration($name);
    
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

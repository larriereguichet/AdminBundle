<?php

namespace LAG\AdminBundle\Field;

use LAG\AdminBundle\Admin\Configuration\ApplicationConfiguration;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
     * Configure options resolver.
     *
     * @param OptionsResolver $resolver
     *
     * @return mixed
     */
    public function configureOptions(OptionsResolver $resolver);

    /**
     * Set options values after options resolving.
     *
     * @param array $options
     *
     * @return mixed
     */
    public function setOptions(array $options);

    /**
     * Field name.
     *
     * @return string
     */
    public function getName();

    /**
     * Define field name.
     *
     * @param $name
     * @return void
     */
    public function setName($name);

    /**
     * Return field type.
     *
     * @return string
     */
    public function getType();

    /**
     * Defines application configuration.
     *
     * @param ApplicationConfiguration $configuration
     */
    public function setConfiguration($configuration);
}

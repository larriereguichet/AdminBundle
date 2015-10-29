<?php

namespace LAG\AdminBundle\Admin;

use Symfony\Component\OptionsResolver\OptionsResolver;

interface FieldInterface
{
    /**
     * Render value of the field
     *
     * @param mixed $value Value to render
     * @return mixed
     */
    public function render($value);

    /**
     * Configure options resolver
     *
     * @param OptionsResolver $resolver
     * @return mixed
     */
    public function configureOptions(OptionsResolver $resolver);

    /**
     * Set options values after options resolving
     *
     * @param array $options
     * @return mixed
     */
    public function setOptions(array $options);

    public function getName();

    public function setName($name);

    public function getType();
}

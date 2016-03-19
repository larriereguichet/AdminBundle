<?php

namespace LAG\AdminBundle\Configuration;


use Symfony\Component\OptionsResolver\OptionsResolver;

interface ConfigurationInterface
{
    /**
     * Configure configuration allowed parameters.
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver);

    /**
     * Define resolved parameters.
     *
     * @param array $parameters
     */
    public function setParameters(array $parameters);

    /**
     * @param string $name
     * @return mixed
     */
    public function getParameter($name);

    /**
     * @return array
     */
    public function getParameters();
}

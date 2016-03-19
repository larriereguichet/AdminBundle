<?php

namespace LAG\AdminBundle\Configuration;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Abstract configuration class.
 */
abstract class Configuration implements ConfigurationInterface
{
    /**
     * @var ParameterBag
     */
    protected $parameters;

    /**
     * Configuration constructor.
     */
    public function __construct()
    {
        $this->parameters = new ParameterBag();
    }

    /**
     * Define allowed parameters and values for this configuration, using optionsResolver component.
     *
     * @param OptionsResolver $resolver
     */
    public abstract function configureOptions(OptionsResolver $resolver);

    /**
     * Return true if the parameter exists.
     *
     * @param string $name
     * @return bool
     */
    public function hasParameter($name)
    {
        return $this
            ->parameters
            ->has($name);
    }

    /**
     * Return the parameter value.
     *
     * @param string $name
     * @return mixed
     */
    public function getParameter($name)
    {
        return $this
            ->parameters
            ->get($name);
    }

    /**
     * Define all resolved parameters values.
     *
     * @param array $parameters
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = new ParameterBag($parameters);
    }

    /**
     * Return all parameters.
     *
     * @return array
     */
    public function getParameters()
    {
        return $this
            ->parameters
            ->all();
    }
}

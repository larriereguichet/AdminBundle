<?php

namespace LAG\AdminBundle\Field;

use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\Translation\TranslatorInterface;
use Twig_Environment;

abstract class Field implements FieldInterface, TranslatableFieldInterface, TwigFieldInterface
{
    const TYPE_STRING = 'string';
    const TYPE_LINK = 'link';
    const TYPE_ARRAY = 'array';
    const TYPE_DATE = 'date';
    const TYPE_COUNT = 'count';
    const TYPE_ACTION = 'action';
    const TYPE_COLLECTION = 'collection';
    const TYPE_BOOLEAN = 'boolean';

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
     * Twig engine
     *
     * @var Twig_Environment
     */
    protected $twig;

    /**
     * Translator
     *
     * @var TranslatorInterface
     */
    protected $translator;

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
     * @param TranslatorInterface $translator
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
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
     * Set twig environment.
     *
     * @param Twig_Environment $twig
     */
    public function setTwig(Twig_Environment $twig)
    {
        $this->twig = $twig;
    }
}

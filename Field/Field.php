<?php

namespace LAG\AdminBundle\Field;

use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
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
     * Name of the field.
     *
     * @var string
     */
    protected $name;

    /**
     * @var ApplicationConfiguration
     */
    protected $configuration;

    /**
     * Twig engine.
     *
     * @var Twig_Environment
     */
    protected $twig;

    /**
     * Translator.
     *
     * @var TranslatorInterface
     */
    protected $translator;

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

    /**
     * @param Twig_Environment $twig
     */
    public function setTwig(Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @param TranslatorInterface $translator
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }
}

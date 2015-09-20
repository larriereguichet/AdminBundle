<?php

namespace BlueBear\AdminBundle\Admin\Configuration;

use Symfony\Component\OptionsResolver\OptionsResolver;

class ApplicationConfiguration
{
    /**
     * Application title
     *
     * @var string
     */
    protected $title;

    /**
     * Application description
     *
     * @var string
     */
    protected $description;

    /**
     * Application locale
     *
     * @var string
     */
    protected $locale;

    /**
     * Admin main twig layout
     *
     * @var string
     */
    protected $layout;

    /**
     * Twig template use for rendering block in forms
     *
     * @var string
     */
    protected $blockTemplate;

    /**
     * Use bootstrap integration
     *
     * @var bool
     */
    protected $bootstrap = false;

    /**
     * Application main date format
     *
     * @var string
     */
    protected $dateFormat;

    /**
     * String length before truncate it (if null, no truncation)
     *
     * @var int
     */
    protected $stringLength;

    /**
     * Replace string in truncation
     *
     * @var
     */
    protected $stringLengthTruncate;

    /**
     * Url routing pattern
     *
     * @var string
     */
    protected $routingUrlPattern;

    /**
     * Generated route name pattern
     *
     * @var string
     */
    protected $routingNamePattern;

    /**
     * Default number of displayed records in list
     *
     * @var int
     */
    protected $maxPerPage;

    public function __construct(array $applicationConfiguration = [], $locale)
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'title' => '',
            'description' => '',
            'locale' => $locale,
            'layout' => 'BlueBearAdminBundle::admin.layout.html.twig',
            'block_template' => 'BlueBearAdminBundle:Form:fields.html.twig',
            'bootstrap' => false,
            'date_format' => 'd/m/Y',
            'string_length' => 0,
            'string_length_truncate' => '...',
            'routing' => [
                'url_pattern' => '/{admin}/{action}',
                'name_pattern' => 'bluebear.admin.{admin}'
            ],
            'max_per_page' => 25
        ]);
        $applicationConfiguration = $resolver->resolve($applicationConfiguration);
        $this->title = $applicationConfiguration['title'];
        $this->description = $applicationConfiguration['description'];
        $this->locale = $applicationConfiguration['locale'];
        $this->title = $applicationConfiguration['title'];
        $this->layout = $applicationConfiguration['layout'];
        $this->blockTemplate = $applicationConfiguration['block_template'];
        $this->bootstrap = $applicationConfiguration['bootstrap'];
        $this->dateFormat = $applicationConfiguration['date_format'];
        $this->stringLength = $applicationConfiguration['string_length'];
        $this->stringLengthTruncate = $applicationConfiguration['string_length_truncate'];
        $this->routingUrlPattern = $applicationConfiguration['routing']['url_pattern'];
        $this->routingNamePattern = $applicationConfiguration['routing']['name_pattern'];
        $this->maxPerPage = $applicationConfiguration['max_per_page'];
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * @param mixed $layout
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    /**
     * @return mixed
     */
    public function getBlockTemplate()
    {
        return $this->blockTemplate;
    }

    /**
     * @param mixed $blockTemplate
     */
    public function setBlockTemplate($blockTemplate)
    {
        $this->blockTemplate = $blockTemplate;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return boolean
     */
    public function useBootstrap()
    {
        return $this->bootstrap;
    }

    /**
     * @param boolean $bootstrap
     */
    public function setBootstrap($bootstrap)
    {
        $this->bootstrap = $bootstrap;
    }

    /**
     * @return mixed
     */
    public function getDateFormat()
    {
        return $this->dateFormat;
    }

    /**
     * @param mixed $dateFormat
     */
    public function setDateFormat($dateFormat)
    {
        $this->dateFormat = $dateFormat;
    }

    /**
     * @return string
     */
    public function getRoutingUrlPattern()
    {
        return $this->routingUrlPattern;
    }

    /**
     * @param string $routingUrlPattern
     */
    public function setRoutingUrlPattern($routingUrlPattern)
    {
        $this->routingUrlPattern = $routingUrlPattern;
    }

    /**
     * @return string
     */
    public function getRoutingNamePattern()
    {
        return $this->routingNamePattern;
    }

    /**
     * @param string $routingNamePattern
     */
    public function setRoutingNamePattern($routingNamePattern)
    {
        $this->routingNamePattern = $routingNamePattern;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * @return int
     */
    public function getStringLength()
    {
        return $this->stringLength;
    }

    /**
     * @param int $stringLength
     */
    public function setStringLength($stringLength)
    {
        $this->stringLength = $stringLength;
    }

    /**
     * @return mixed
     */
    public function getStringLengthTruncate()
    {
        return $this->stringLengthTruncate;
    }

    /**
     * @param mixed $stringLengthTruncate
     */
    public function setStringLengthTruncate($stringLengthTruncate)
    {
        $this->stringLengthTruncate = $stringLengthTruncate;
    }

    /**
     * @return boolean
     */
    public function isBootstrap()
    {
        return $this->bootstrap;
    }

    /**
     * @return int
     */
    public function getMaxPerPage()
    {
        return $this->maxPerPage;
    }

    /**
     * @param int $maxPerPage
     */
    public function setMaxPerPage($maxPerPage)
    {
        $this->maxPerPage = $maxPerPage;
    }
}

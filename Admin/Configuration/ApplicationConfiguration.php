<?php

namespace LAG\AdminBundle\Admin\Configuration;

use LAG\AdminBundle\Admin\Field;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
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
     * @var int
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

    /**
     * @var bool
     */
    protected $useTranslation = true;

    /**
     * Pattern use for translation key (ie: lag.admin.{key}, admin will
     *
     * @var string
     */
    protected $translationPattern;

    public function __construct(array $applicationConfiguration = [], $locale)
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'title' => '',
            'description' => '',
            'locale' => $locale,
            'layout' => 'LAGAdminBundle::admin.layout.html.twig',
            'block_template' => 'LAGAdminBundle:Form:fields.html.twig',
            'bootstrap' => false,
            'date_format' => 'd/m/Y',
            'string_length' => 0,
            'string_length_truncate' => '...',
            'routing' => [
                'url_pattern' => '/{admin}/{action}',
                'name_pattern' => 'lag.admin.{admin}'
            ],
            'translation' => [
                'enabled' => true,
                'pattern' => 'lag.admin.{key}',
            ],
            'max_per_page' => 25,
            'fields_mapping' => [
                Field::TYPE_STRING => 'lag.admin.field.string',
                Field::TYPE_ARRAY => 'LAG\AdminBundle\Admin\Render\ArrayRenderer',
                Field::TYPE_LINK => 'LAG\AdminBundle\Admin\Render\LinkRenderer',
                Field::TYPE_DATE => 'LAG\AdminBundle\Admin\Render\DateRenderer',
                Field::TYPE_COUNT => 'LAG\AdminBundle\Admin\Render\CountRenderer',
                Field::TYPE_ACTION => 'LAG\AdminBundle\Admin\Render\LinkRenderer',
            ]
        ]);
        $applicationConfiguration = $resolver->resolve($applicationConfiguration);
        // resolving routing options
        $routingConfiguration = $applicationConfiguration['routing'];
        $resolver->clear();
        $resolver->setRequired([
            'url_pattern',
            'name_pattern',
        ]);
        $resolver->setNormalizer('url_pattern', function (Options $options, $value) {
            if (strstr($value, '{admin}') === false) {
                throw new InvalidOptionsException('Admin routing configuration url pattern should contains {admin} placeholder');
            }
            if (strstr($value, '{action}') === false) {
                throw new InvalidOptionsException('Admin routing configuration url pattern should contains {action} placeholder');
            }
            return $value;
        });
        $resolver->setNormalizer('name_pattern', function (Options $options, $value) {
            if (strstr($value, '{admin}') === false) {
                throw new InvalidOptionsException('Admin routing configuration pattern name should contains {admin} placeholder');
            }
            return $value;
        });
        $routingConfiguration = $resolver->resolve($routingConfiguration);
        // routing configuration
        $this->routingUrlPattern = $routingConfiguration['url_pattern'];
        $this->routingNamePattern = $routingConfiguration['name_pattern'];

        // resolving translation configuration
        $translationConfiguration = $applicationConfiguration['translation'];
        $resolver
            ->clear()
            ->setDefault('enabled', true)
            ->setDefault('pattern', 'lag.admin.{key}');
        $resolver->setNormalizer('pattern', function (Options $options, $value) {
            if (strstr($value, 'key') === false) {
                throw new InvalidOptionsException('Admin translation configuration pattern should contains {key} placeholder');
            }
            return $value;
        });
        $translationConfiguration = $resolver->resolve($translationConfiguration);
        // translation configuration
        $this->useTranslation = $translationConfiguration['enabled'];
        $this->translationPattern = $translationConfiguration['pattern'];

        // application main configuration
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

    public function getJavascriptDateFormat()
    {
        $jsDateFormat = str_replace('Y', 'yyyy', $this->getDateFormat());
        $jsDateFormat = str_replace('mm', 'ii', $jsDateFormat);
        $jsDateFormat = str_replace('MM', 'mm', $jsDateFormat);
        $jsDateFormat = str_replace('HH', 'hh', $jsDateFormat);
        $jsDateFormat = str_replace('d', 'dd', $jsDateFormat);

        return $jsDateFormat;
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

    /**
     * @return string
     */
    public function getTranslationPattern()
    {
        return $this->translationPattern;
    }

    /**
     * @param string $translationPattern
     */
    public function setTranslationPattern($translationPattern)
    {
        $this->translationPattern = $translationPattern;
    }

    public function getTranslationKey($key, $adminName = null)
    {
        if (strstr($this->translationPattern, '{admin}') && $adminName) {
            $key = str_replace('{admin}', $adminName, $this->translationPattern);
        }
        $key = str_replace('{key}', $key, $this->translationPattern);

        return $key;
    }

    /**
     * @return boolean
     */
    public function useTranslation()
    {
        return $this->useTranslation;
    }

    /**
     * @param boolean $useTranslation
     */
    public function setUseTranslation($useTranslation)
    {
        $this->useTranslation = $useTranslation;
    }
}

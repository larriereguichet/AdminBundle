<?php

namespace LAG\AdminBundle\Field\Field;

use LAG\AdminBundle\Field\AbstractField;
use LAG\AdminBundle\Field\Configuration\StringFieldConfiguration;
use LAG\AdminBundle\Field\TranslatorAwareInterface;
use LAG\AdminBundle\Field\TwigAwareInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Twig_Environment;

/**
 * String field.
 *
 * It render a string that can be truncated according to is length
 * Note : according php7 (nightly), class can not be named String anymore
 */
class StringField extends AbstractField implements TranslatorAwareInterface, TwigAwareInterface
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;
    
    /**
     * @var Twig_Environment
     */
    protected $twig;

    /**
     * Render a string that can be truncated according to is length.
     *
     * @param $value
     * @return string
     */
    public function render($value)
    {
        if ($this->options['translation']) {
            $value = $this
                ->translator
                ->trans($value)
            ;
        }
        $maximumStringLength = $this->options['length'];
        $replaceString = $this->options['replace'];

        // Truncate string if required
        if ($maximumStringLength && mb_strlen($value) > $maximumStringLength) {
            $value = mb_substr($value, 0, $maximumStringLength).$replaceString;
        }
        // #69 : strip tags to avoid to break the layout when content contains html
        $value = strip_tags($value);

        return $value;
    }

    /**
     * Return form type.
     *
     * @return string
     */
    public function getType()
    {
        return 'string';
    }
    
    /**
     * Defines translator.
     *
     * @param TranslatorInterface $translator
     *
     * @return void
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }
    
    /**
     * Define twig environment.
     *
     * @param Twig_Environment $twig
     *
     * @return void
     */
    public function setTwig(Twig_Environment $twig)
    {
        $this->twig = $twig;
    }
    
    /**
     * Return the Field's configuration class.
     *
     * @return string
     */
    public function getConfigurationClass()
    {
        return StringFieldConfiguration::class;
    }
}

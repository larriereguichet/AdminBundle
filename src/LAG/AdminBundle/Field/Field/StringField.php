<?php

namespace LAG\AdminBundle\Field\Field;

use LAG\AdminBundle\Field\AbstractField;
use LAG\AdminBundle\Field\TranslatorAwareInterface;
use LAG\AdminBundle\Field\TwigAwareInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
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
        if ($this->options->get('translation')) {
            $value = $this
                ->translator
                ->trans($value);
        }
        $maximumStringLength = $this
            ->options
            ->get('length');
        $replaceString = $this
            ->options
            ->get('replace');

        // truncate string if required
        if ($maximumStringLength && strlen($value) > $maximumStringLength) {
            $value = substr($value, 0, $maximumStringLength).$replaceString;
        }
        // #69 : strip tags to avoid layout destruction when content contains html
        $value = strip_tags($value);

        return $value;
    }

    /**
     * Configure options resolver.
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        
        $resolver->setDefaults([
            'length' => $this->applicationConfiguration->getParameter('string_length'),
            'replace' => $this->applicationConfiguration->getParameter('string_length_truncate'),
            'translation' => true,
        ]);
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
}

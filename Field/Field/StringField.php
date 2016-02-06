<?php

namespace LAG\AdminBundle\Field\Field;

use LAG\AdminBundle\Field\Field;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * String field.
 *
 * It render a string that can be truncated according to is length
 * Note : according php7 (nightly), class can not be named String anymore
 */
class StringField extends Field
{
    /**
     * Length before truncate the string. If null, no truncation will be made.
     *
     * @var int
     */
    protected $length;

    /**
     * String use for replace truncated part.
     *
     * @var string
     */
    protected $replace;

    /**
     * True if value should be translated at render.
     *
     * @var bool
     */
    protected $translation;

    /**
     * Translator.
     *
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * Render a string that can be truncated according to is length.
     *
     * @param $value
     * @return string
     */
    public function render($value)
    {
        if ($this->translation) {
            $value = $this->translator->trans($value);
        }
        if ($this->length && strlen($value) > $this->length) {
            $value = substr($value, 0, $this->length) . $this->replace;
        }

        return $value;
    }

    /**
     * Configure options resolver.
     *
     * @param OptionsResolver $resolver
     * @return mixed|void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'length' => $this->configuration->getStringLength(),
            'replace' => $this->configuration->getStringLengthTruncate(),
            'translation' => true,
        ]);
    }

    /**
     * Define configured options
     *
     * @param array $options
     * @return void
     */
    public function setOptions(array $options)
    {
        $this->length = (int)$options['length'];
        $this->replace = $options['replace'];
        $this->translation = $options['translation'];
    }

    /**
     * Define translator.
     *
     * @param TranslatorInterface $translator
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
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
}

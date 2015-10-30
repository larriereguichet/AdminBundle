<?php

namespace LAG\AdminBundle\Admin\Field;

use LAG\AdminBundle\Admin\Field;
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
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * Render a string that can be truncated according to is length.
     *
     * @param $value
     *
     * @return string
     */
    public function render($value)
    {
        if ($this->length && strlen($value) > $this->length) {
            $value = substr($value, 0, $this->length).$this->replace;
        }
        if ($this->translation) {
            $value = $this->translator->trans($value);
        }

        return $value;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'length' => $this->configuration->getStringLength(),
            'replace' => $this->configuration->getStringLengthTruncate(),
            'translation' => true,
        ]);
    }

    public function setOptions(array $options)
    {
        $this->length = (int) $options['length'];
        $this->replace = $options['replace'];
        $this->translation = $options['translation'];
    }

    /**
     * @param TranslatorInterface $translator
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function getType()
    {
        return 'string';
    }
}

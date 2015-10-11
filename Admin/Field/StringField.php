<?php

namespace BlueBear\AdminBundle\Admin\Field;

use BlueBear\AdminBundle\Admin\Field;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * String field. It render a string that can be truncated according to is length
 */
class StringField extends Field
{
    /**
     * Length before truncate the string. If null, no truncation will be made
     *
     * @var int
     */
    protected $length;

    /**
     * String use for replace truncated part
     *
     * @var string
     */
    protected $replace;

    /**
     * Render a string that can be truncated according to is length
     *
     * @param $value
     * @return string
     */
    public function render($value)
    {
        if ($this->length && strlen($value) > $this->length) {
            $value = substr($value, 0, $this->length) . $this->replace;
        }
        return $value;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'length' => $this->configuration->getStringLength(),
            'replace' => $this->configuration->getStringLengthTruncate()
        ]);
    }

    public function setOptions(array $options)
    {
        $this->length = (int)$options['length'];
        $this->replace = $options['replace'];
    }

    public function getType()
    {
        return 'string';
    }
}

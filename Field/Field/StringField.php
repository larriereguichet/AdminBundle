<?php

namespace LAG\AdminBundle\Field\Field;

use LAG\AdminBundle\Field\Field;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * String field.
 *
 * It render a string that can be truncated according to is length
 * Note : according php7 (nightly), class can not be named String anymore
 */
class StringField extends Field
{

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
            $value = substr($value, 0, $maximumStringLength) . $replaceString;
        }

        return $value;
    }

    /**
     * Configure options resolver.
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
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
}

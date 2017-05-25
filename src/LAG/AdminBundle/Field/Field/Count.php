<?php

namespace LAG\AdminBundle\Field\Field;

use LAG\AdminBundle\Field\AbstractField;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Count field.
 */
class Count extends AbstractField
{
    /**
     * String displayed if rendered value is empty (or null or 0).
     *
     * @var string
     */
    protected $emptyString;

    public function render($value)
    {
        $count = count($value);

        if ($count > 0 || $this->emptyString === null) {
            $render = $count;
        } else {
            $render = $this->emptyString;
        }

        return $render;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'empty_string' => null,
        ]);
    }

    public function setOptions(array $options)
    {
        $this->emptyString = $options['empty_string'];
    }

    public function getType()
    {
        return 'count';
    }
}
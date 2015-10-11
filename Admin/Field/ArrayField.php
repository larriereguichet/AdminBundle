<?php

namespace BlueBear\AdminBundle\Admin\Field;

use BlueBear\AdminBundle\Admin\Field;
use Exception;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Traversable;

class ArrayField extends Field
{
    protected $glue;

    public function render($value)
    {
        if (!is_array($value) || $value instanceof Traversable) {
            throw new Exception('Value should be an array instead of ' . gettype($value));
        }
        return implode($this->glue, $value);
    }

    /**
     * Configure options resolver
     *
     * @param OptionsResolver $resolver
     * @return mixed
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'glue' => ', '
        ]);
    }

    /**
     * Set options values after options resolving
     *
     * @param array $options
     * @return mixed
     */
    public function setOptions(array $options)
    {
        $this->glue = $options['glue'];
    }

    public function getType()
    {
        return 'array';
    }
}

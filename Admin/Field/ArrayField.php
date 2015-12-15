<?php

namespace LAG\AdminBundle\Admin\Field;

use Doctrine\Common\Collections\Collection;
use LAG\AdminBundle\Admin\Field;
use Exception;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Traversable;

/**
 * Array field.
 *
 * Note : class can not be called Array by php restriction
 */
class ArrayField extends Field
{
    protected $glue;

    /**
     * Render field value
     *
     * @param mixed $value
     * @return string
     * @throws Exception
     */
    public function render($value)
    {
        if (!is_array($value) && !($value instanceof Traversable)) {
            throw new Exception('Value should be an array instead of '.gettype($value));
        }
        if ($value instanceof Collection) {
            $value = $value->toArray();
        }

        return implode($this->glue, $value);
    }

    /**
     * Configure options resolver.
     *
     * @param OptionsResolver $resolver
     *
     * @return mixed
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'glue' => ', ',
        ]);
    }

    /**
     * Set options values after options resolving.
     *
     * @param array $options
     *
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

<?php

namespace LAG\AdminBundle\Field\Field;

use Doctrine\Common\Collections\Collection as DoctrineCollection;
use LAG\AdminBundle\Field\AbstractField;
use Exception;
use LAG\AdminBundle\Field\Configuration\ArrayFieldConfiguration;
use Traversable;

/**
 * Array field.
 *
 * Note : class can not be called Array by php restriction
 */
class ArrayField extends AbstractField
{
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
        if ($value instanceof DoctrineCollection) {
            $value = $value->toArray();
        }

        return implode($this->options['glue'], $value);
    }
    
    /**
     * @return string
     */
    public function getType()
    {
        return 'array';
    }
    
    /**
     * Return the Field's configuration class.
     *
     * @return string
     */
    public function getConfigurationClass()
    {
        return ArrayFieldConfiguration::class;
    }
}

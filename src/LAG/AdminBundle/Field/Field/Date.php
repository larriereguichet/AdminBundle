<?php

namespace LAG\AdminBundle\Field\Field;

use LAG\AdminBundle\Field\AbstractField;
use DateTime;
use LAG\AdminBundle\Field\Configuration\DateConfiguration;

/**
 * Date field.
 */
class Date extends AbstractField
{
    /**
     * Render and format a Datetime.
     *
     * @param mixed $value
     *
     * @return string
     *
     * @throws \Exception
     */
    public function render($value)
    {
        if (null === $value) {
            return '';
        }

        if (!$value instanceof DateTime) {
            throw new \Exception('Expected Datetime, got '.gettype($value));
        }

        return $value->format($this->options['format']);
    }
    
    /**
     * @return string
     */
    public function getType()
    {
        return 'date';
    }
    
    /**
     * Return the Field's configuration class.
     *
     * @return string
     */
    public function getConfigurationClass()
    {
        return DateConfiguration::class;
    }
}

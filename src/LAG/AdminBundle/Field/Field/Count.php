<?php

namespace LAG\AdminBundle\Field\Field;

use LAG\AdminBundle\Field\AbstractField;
use LAG\AdminBundle\Field\Configuration\CountConfiguration;

/**
 * Count field.
 */
class CountOLD extends AbstractFieldOLD
{
    public function render($value)
    {
        $count = count($value);
    
        if ($count > 0 || $this->options['empty_string'] === null) {
            $render = $count;
        } else {
            $render = $this->options['empty_string'];
        }
    
        return $render;
    }
    
    public function getType()
    {
        return 'count';
    }
    
    /**
     * Return the Field's configuration class.
     *
     * @return string
     */
    public function getConfigurationClass()
    {
        return CountConfiguration::class;
    }
}

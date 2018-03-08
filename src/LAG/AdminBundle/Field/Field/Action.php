<?php

namespace LAG\AdminBundle\Field\Field;

use LAG\AdminBundle\Field\Configuration\ActionConfiguration;

class ActionOLD extends Link
{
    /**
     * Display a link to an Action.
     *
     * @param string $value
     *
     * @return string
     */
    public function render($value)
    {
        if (null !== $this->options['title']) {
            $value = $this->options['title'];
        }
    
        return parent::render($value);
    }
    
    /**
     * @inheritdoc
     *
     * @return string
     */
    public function getType()
    {
        return 'action';
    }
    
    /**
     * Return the Field's configuration class.
     *
     * @return string
     */
    public function getConfigurationClass()
    {
        return ActionConfiguration::class;
    }
}

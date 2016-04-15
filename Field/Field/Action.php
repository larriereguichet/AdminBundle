<?php

namespace LAG\AdminBundle\Field\Field;

class Action extends Link
{
    /**
     * @param string $value
     * @return string
     */
    public function render($value)
    {
        $value = $this
            ->options
            ->get('title');

        return parent::render($value);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'action';
    }
}

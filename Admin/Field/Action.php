<?php

namespace LAG\AdminBundle\Admin\Field;

class Action extends Link
{
    public function render($value)
    {
        $value = $this->title;

        return parent::render($value);
    }

    public function getType()
    {
        return 'action';
    }
}

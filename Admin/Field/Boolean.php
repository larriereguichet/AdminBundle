<?php

namespace LAG\AdminBundle\Admin\Field;

class Boolean extends StringField
{
    public function render($value)
    {
        $result = filter_var($value, FILTER_VALIDATE_BOOLEAN);
        $text = ($result) ? 'lag.admin.true' : 'lag.admin.false';

        return parent::render($text);
    }

    public function getType()
    {
        return 'boolean';
    }
}

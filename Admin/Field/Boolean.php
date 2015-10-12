<?php

namespace BlueBear\AdminBundle\Admin\Field;


class Boolean extends StringField
{
    public function render($value)
    {
        $result = filter_var($value, FILTER_VALIDATE_BOOLEAN);
        $text = ($result) ? 'bluebear.admin.true' : 'bluebear.admin.false';

        return parent::render($text);
    }

    public function getType()
    {
        return 'boolean';
    }
}

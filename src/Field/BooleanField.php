<?php

namespace LAG\AdminBundle\Field;

class BooleanField extends AbstractField
{
    public function isSortable(): bool
    {
        return true;
    }

    public function render($value): string
    {
        if (true === $value) {
            $content = '<i class="fa fa-check text-success"></i>';
        } else {
            $content = '<i class="fa fa-times text-danger"></i>';
        }

        return $content;
    }
}

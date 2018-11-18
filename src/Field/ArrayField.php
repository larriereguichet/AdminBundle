<?php

namespace LAG\AdminBundle\Field;

class ArrayField extends AbstractField
{
    public function isSortable(): bool
    {
        return false;
    }

    public function render($value = null): string
    {
        return 'in progress';
    }
}

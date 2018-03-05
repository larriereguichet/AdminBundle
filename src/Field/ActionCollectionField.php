<?php

namespace LAG\AdminBundle\Field;

class ActionCollectionField extends AbstractField
{
    public function isSortable(): bool
    {
        return false;
    }

    public function render($value): string
    {
        return 'in progress';
    }
}

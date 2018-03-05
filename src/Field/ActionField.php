<?php

namespace LAG\AdminBundle\Field;

class ActionField extends LinkField
{
    public function isSortable(): bool
    {
        return false;
    }
}

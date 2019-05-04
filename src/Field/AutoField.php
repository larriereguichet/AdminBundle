<?php

namespace LAG\AdminBundle\Field;

class AutoField extends AbstractField
{
    public function render($value = null): string
    {
        if (null === $value) {
            return '';
        }

        if (is_string($value)) {
            return $value;
        }

        if (is_array($value)) {
            return implode(',', $value);
        }

        if (is_object($value)) {
            if (method_exists($value, '__toString')) {
                return (string) $value;
            } else {
                return serialize($value);
            }
        }

        return '';
    }
}

<?php

namespace LAG\AdminBundle\Field;

use Doctrine\Common\Collections\Collection;

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
                if ($value instanceof Collection) {
                    $value = $value->toArray();
                }

                if (is_array($value)) {
                    return implode(',', $value);
                }
            }
        }

        return '';
    }
}

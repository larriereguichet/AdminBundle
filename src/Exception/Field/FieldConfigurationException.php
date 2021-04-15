<?php

namespace LAG\AdminBundle\Exception\Field;

use Throwable;

class FieldConfigurationException extends FieldException
{
    public function __construct(string $fieldName, array $context = [], string $error = '', Throwable $previous = null)
    {
        $message = sprintf(
            'An error occurred when configuring the field "%s" : "%s". The field context is "%s" ',
            $fieldName,
            $error,
            print_r($context, true)
        );

        parent::__construct($message, 0, $previous);
    }
}

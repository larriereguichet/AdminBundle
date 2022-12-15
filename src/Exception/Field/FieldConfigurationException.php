<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Exception\Field;

class FieldConfigurationException extends FieldException
{
    public function __construct(string $fieldName, string $error = '', \Throwable $previous = null)
    {
        $message = sprintf(
            'An error occurred when configuring the field "%s" : "%s".',
            $fieldName,
            $error
        );

        parent::__construct($message, 0, $previous);
    }
}

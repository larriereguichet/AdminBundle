<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Exception\Field;

class FieldTypeNotFoundException extends FieldException
{
    public function __construct(string $type, string $fieldName, array $context = [])
    {
        $message = sprintf(
            'The type "%s" is not configured for the Field "%s" with context "%s"',
            $type,
            $fieldName,
            print_r($context, true)
        );

        parent::__construct($message, 500);
    }
}

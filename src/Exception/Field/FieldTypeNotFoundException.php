<?php

namespace LAG\AdminBundle\Exception\Field;

use Exception;

class FieldTypeNotFoundException extends Exception
{
    public function __construct(string $adminName, string $actionName, string $fieldName)
    {
        $message = sprintf(
            'No type found for the Field "%s" in Action "%s" in Admin "%s"',
            $fieldName,
            $actionName,
            $adminName
        );

        parent::__construct($message, 500);
    }
}

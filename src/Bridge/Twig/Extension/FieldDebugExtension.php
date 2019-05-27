<?php

namespace LAG\AdminBundle\Bridge\Twig\Extension;

use Exception;
use LAG\AdminBundle\Field\FieldInterface;

class FieldDebugExtension extends FieldExtension
{
    public function renderField(FieldInterface $field, $entity): string
    {
        try {
            return parent::renderField($field, $entity);
        } catch (Exception $exception) {
            $message = sprintf(
                'An error has occurred when rendering the field "%s" : %s',
                $field->getName(),
                $exception->getMessage()
            );
            throw new Exception($message, 0, $exception);
        }
    }
}

<?php

namespace LAG\AdminBundle\Utils;

class FieldTypeGuesser
{
    /**
     * Return an array containing the field type and its default configuration for the given Doctrine type.
     *
     * @param $doctrineType
     * @return array
     */
    public function getTypeAndOptions($doctrineType)
    {
        $fieldOptions = [];

        if ($doctrineType == 'string') {
            $fieldOptions = [
                'type' => 'string',
                'options' => [
                    'length' => 100
                ]
            ];
        } else if ($doctrineType == 'boolean') {
            $fieldOptions = [
                'type' => 'boolean',
                'options' => []
            ];
        } else if ($doctrineType == 'datetime') {
            $fieldOptions = [
                'type' => 'date',
                'options' => []
            ];
        }
        return $fieldOptions;
    }
}

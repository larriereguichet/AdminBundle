<?php

namespace LAG\AdminBundle\Utils;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class FormUtils
{
    /**
     * Convert a shortcut type into its class type.
     *
     * @param string $type
     *
     * @return string
     */
    public static function convertShortFormType(string $type)
    {
        $mapping = [
            'choice' => ChoiceType::class,
            'array' => ChoiceType::class,
            'string' => TextType::class,
            'entity' => EntityType::class,
            'date' => DateType::class,
            'datetime' => DateType::class,
            'text' => TextType::class,
            'number' => NumberType::class,
            'integer' => NumberType::class,
            'smallint' => NumberType::class,
            'boolean' => CheckboxType::class,
        ];

        if (key_exists($type, $mapping)) {
            $type = $mapping[$type];
        }

        return $type;
    }
}

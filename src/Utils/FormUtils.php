<?php

namespace LAG\AdminBundle\Utils;

use LAG\AdminBundle\Form\Type\DateRangeType;
use LAG\AdminBundle\Form\Type\Select2\Select2EntityType;
use LAG\AdminBundle\Form\Type\Select2\Select2Type;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class FormUtils
{
    /**
     * Convert a shortcut type into its class type.
     */
    public static function convertShortFormType(?string $shortType): ?string
    {
        $mapping = [
            'choice' => Select2Type::class,
            'string' => TextType::class,
            'entity' => Select2EntityType::class,
            'date' => DateRangeType::class,
            'datetime' => DateRangeType::class,
            'text' => TextareaType::class,
            'number' => NumberType::class,
            'float' => NumberType::class,
            'integer' => IntegerType::class,
            'smallint' => IntegerType::class,
            'boolean' => CheckboxType::class,
            'bigint' => NumberType::class,
            'decimal' => NumberType::class,
            'guid' => TextType::class,
            'array' => TextareaType::class,
            'simple_array' => TextareaType::class,
            'json_array' => TextareaType::class,
            'file' => FileType::class,
            'upload' => FileType::class,
        ];
        $type = $shortType;

        if (\array_key_exists($shortType, $mapping)) {
            $type = $mapping[$shortType];
        }

        return $type;
    }

    public static function getFormTypeOptions(?string $type): array
    {
        $mapping = [];
        $options = [];

        if (\array_key_exists($type, $mapping)) {
            $options = $mapping[$type];
        }

        return $options;
    }
}

<?php

namespace LAG\AdminBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class CollectionExtensionType extends AbstractTypeExtension
{
    public static function getExtendedTypes(): iterable
    {
        return [
            CollectionType::class,
        ];
    }
}

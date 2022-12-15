<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Field;

use Symfony\Component\OptionsResolver\OptionsResolver;

class CountField extends AbstractField
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'template' => '@LAGAdmin/fields/string.html.twig',
            'translation' => false,
            'empty_string' => null,
        ]);
    }

    public function getDataTransformer(): ?\Closure
    {
        return function ($data) {
            return is_countable($data) ? \count($data) : 0;
        };
    }
}

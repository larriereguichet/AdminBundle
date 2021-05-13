<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Field;

use Symfony\Component\OptionsResolver\OptionsResolver;

class MappedField extends AbstractField
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'template' => '@LAGAdmin/fields/mapped.html.twig',
                'translation' => true,
            ])
            ->setRequired('map')
            ->setAllowedTypes('map', 'array')
        ;
    }
}

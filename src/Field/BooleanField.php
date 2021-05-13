<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Field;

use Symfony\Component\OptionsResolver\OptionsResolver;

class BooleanField extends AbstractField
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'template' => '@LAGAdmin/fields/boolean.html.twig',
        ]);
    }
}

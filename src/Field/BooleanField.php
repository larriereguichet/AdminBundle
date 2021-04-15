<?php

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

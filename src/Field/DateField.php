<?php

namespace LAG\AdminBundle\Field;

use Symfony\Component\OptionsResolver\OptionsResolver;

class DateField extends AbstractField
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'format' => 'd/m/Y',
            'template' => '@LAGAdmin/fields/date.html.twig',
        ]);
    }
}

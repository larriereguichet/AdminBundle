<?php

namespace LAG\AdminBundle\Field;

use Symfony\Component\OptionsResolver\OptionsResolver;

class StringField extends AbstractField
{
    public function configureOptions(OptionsResolver $resolver): void
    {
//        $resolver->setDefaults([
//            'length' => $this->getActionConfiguration()->get('string_length'),
//            'replace' => $this->getActionConfiguration()->get('string_truncate'),
//            'sortable' => true,
//            'template' => '@LAGAdmin/fields/string.html.twig',
//            'translation' => false,
//            'translate_title' => true,
//        ]);

        $resolver->setDefaults([
            'length' => 100,
            'replace' => '...',
            'sortable' => true,
            'template' => '@LAGAdmin/fields/string.html.twig',
            'translation' => false,
            'translate_title' => true,
        ]);
    }
}

<?php

namespace LAG\AdminBundle\Field;

use Symfony\Component\OptionsResolver\OptionsResolver;

class StringField extends AbstractField implements ApplicationAwareInterface
{
    use ApplicationAware;

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'length' => $this->applicationConfiguration->getStringLength(),
            'replace' => $this->applicationConfiguration->getStringTruncate(),
            'sortable' => true,
            'template' => '@LAGAdmin/fields/string.html.twig',
            'translation' => false,
            'translate_title' => true,
        ]);
    }
}

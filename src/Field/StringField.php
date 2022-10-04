<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Field;

use Symfony\Component\OptionsResolver\OptionsResolver;

class StringField extends AbstractField
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

<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Field;

use Symfony\Component\OptionsResolver\OptionsResolver;

class DateField extends AbstractField implements ApplicationAwareInterface
{
    use ApplicationAware;

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'format' => '$this->applicationConfiguration->getDateFormat()',
            'template' => '@LAGAdmin/fields/date.html.twig',
        ]);
    }
}

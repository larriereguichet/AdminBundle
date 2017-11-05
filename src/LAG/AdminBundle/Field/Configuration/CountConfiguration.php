<?php

namespace LAG\AdminBundle\Field\Configuration;

use JK\Configuration\Configuration;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Count field.
 */
class CountConfiguration extends Configuration
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'empty_string' => null,
            'sortable' => false,
        ]);
    }
}

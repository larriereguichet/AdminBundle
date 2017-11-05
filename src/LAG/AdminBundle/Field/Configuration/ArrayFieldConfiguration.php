<?php

namespace LAG\AdminBundle\Field\Configuration;

use JK\Configuration\Configuration;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArrayFieldConfiguration extends Configuration
{
    /**
     * Configure options resolver.
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'glue' => ', ',
        ]);
    }
}

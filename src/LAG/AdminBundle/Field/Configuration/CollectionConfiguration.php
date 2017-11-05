<?php

namespace LAG\AdminBundle\Field\Configuration;

use JK\Configuration\Configuration;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CollectionConfiguration extends Configuration
{
    /**
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired('fields')
            ->setAllowedTypes('fields', 'array')
        ;
    }
}

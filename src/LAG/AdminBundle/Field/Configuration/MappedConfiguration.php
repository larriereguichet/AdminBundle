<?php

namespace LAG\AdminBundle\Field\Configuration;

use JK\Configuration\Configuration;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MappedConfiguration extends Configuration
{
    /**
     * Configure options resolver.
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired('mapping')
            ->setAllowedTypes('mapping', 'array')
        ;
    }
}

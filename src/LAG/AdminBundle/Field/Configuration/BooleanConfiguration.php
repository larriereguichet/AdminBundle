<?php

namespace LAG\AdminBundle\Field\Configuration;

use JK\Configuration\Configuration;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BooleanConfiguration extends Configuration
{
    /**
     * Define allowed parameters and values for this configuration, using optionsResolver component.
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
    }
}

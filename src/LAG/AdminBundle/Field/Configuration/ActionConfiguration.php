<?php

namespace LAG\AdminBundle\Field\Configuration;

use Symfony\Component\OptionsResolver\OptionsResolver;

class ActionConfiguration extends LinkConfiguration
{
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        
        $resolver
            ->setDefaults([
                'class' => 'btn btn-danger btn-sm',
                'text' => '',
            ])
        ;
    }
}

<?php

namespace LAG\AdminBundle\Field\Field;

use Symfony\Component\OptionsResolver\OptionsResolver;

class Action extends Link
{
    /**
     * Display a link to an Action.
     *
     * @param string $value
     *
     * @return string
     */
    public function render($value)
    {
        $value = $this
            ->options
            ->get('title')
        ;
        
        return parent::render($value);
    }
    
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
    
    /**
     * @inheritdoc
     *
     * @return string
     */
    public function getType()
    {
        return 'action';
    }
}

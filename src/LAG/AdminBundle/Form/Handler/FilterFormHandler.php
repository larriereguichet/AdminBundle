<?php

namespace LAG\AdminBundle\Form\Handler;

use Symfony\Component\Form\FormInterface;

class FilterFormHandler
{
    /**
     * @param FormInterface $form
     *
     * @return array
     */
    public function handle(FormInterface $form)
    {
        // if the form is not valid, nothing to do
        if ($form->isValid()) {
            return [];
        }
        // remove the null value
        $filters = array_filter($form->getData(), function($value) {
            return null !== $value;
        });
    
        return $filters;
    }
}

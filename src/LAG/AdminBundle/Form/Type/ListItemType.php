<?php

namespace LAG\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

class ListItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', CheckboxType::class)
            ->addModelTransformer(new CallbackTransformer(function ($value) {
                return $value->getId();
                var_dump($value);
                die;
            }, function () {
                
            }))
        ;
    }
}

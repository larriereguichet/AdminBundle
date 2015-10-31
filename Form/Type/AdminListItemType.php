<?php

namespace LAG\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminListItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options = [])
    {
        $builder->add('id', 'choice', [
            'data' => 'lol',
            'expanded' => true,
            'multiple' => true,
            'required' => false,
            //'label' => false
        ]);

        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $data = $event->getData();
                $form = $event->getForm();

//                $form->add('id', 'choice', [
//                    'data' => 'lol',
//                    'expanded' => true,
//                    'multiple' => true,
//                    'required' => false,
//                    //'label' => false
//                ]);
            });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'coumpound' => true
        ]);
    }

    public function getName()
    {
        return 'admin_list_item';
    }
}

<?php

namespace LAG\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BatchActionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('batch_action', 'hidden')
            ->add('entity_ids', 'collection', [
                'type' => 'hidden',
                'label' => false,
                'allow_add' => true
            ]);
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['labels'] = $options['labels'];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'labels' => []
            ]);
    }

    public function getName()
    {
        return 'batch_action';
    }
}

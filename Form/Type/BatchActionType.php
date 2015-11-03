<?php

namespace LAG\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class BatchActionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('batch_action', 'hidden')
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $form) {
                $data = $form->getData();
                $form = $form->getForm();

                if (!empty($data['entities_ids'])) {
                    $form->add('batch', 'collection', [
                        'type' => 'hidden',
                        'label' => false,
                        'data' => $data['entities_ids']
                    ]);
                }
            });
    }

    public function getName()
    {
        return 'batch_action';
    }
}

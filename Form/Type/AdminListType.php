<?php

namespace LAG\AdminBundle\Form\Type;

use BlueBear\BaseBundle\Entity\Behaviors\Id;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminListType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options = [])
    {
        if (count($options['batch_actions'])) {
            $builder
                ->add('batch', 'choice', [
                    'choices' => $options['batch_actions'],
                    'empty_data' => $options['batch_empty'],
                    'empty_value' => $options['batch_empty']
                ])
                ->add('batch_submit', 'submit', [
                    'label' => $options['batch_actions_label']
                ]);
            $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $data = $event->getData();
                $form = $event->getForm();

                if (!empty($data['entities'])) {
                    /** @var Id $entity */
                    foreach ($data['entities'] as $entity) {
                        $form->add('batch_' . $entity->getId(), 'checkbox', [
                            'value' => $entity->getId(),
                            'label' => false,
                            'required' => false
                        ]);
                    }
                }
            });
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'batch_actions' => [],
                'batch_actions_label' => 'lag.admin.batch_action',
                'batch_empty' => 'lag.admin.batch_empty',
            ]);
    }

    public function getName()
    {
        return 'admin_list';
    }
}

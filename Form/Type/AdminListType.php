<?php

namespace LAG\AdminBundle\Form\Type;

use BlueBear\BaseBundle\Entity\Behaviors\Id;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Menu\Configuration\MenuItemConfiguration;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminListType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options = [])
    {
        if (count($options['batch_actions'])) {
            $builder
                ->add('batch_action', ChoiceType::class, [

                    'choices' => $this->buildBatchSelectChoices($options['batch_actions']),
                    'empty_data' => $options['batch_empty'],
                ])
                ->add('batch_submit', SubmitType::class, [
                    'label' => $options['batch_actions_label']
                ])
                ->add('select_all', CheckboxType::class, [
                    'label' => false,
                    'required' => false,
                    'mapped' => false,
                    'attr' => [
                        'class' => 'select-all'
                    ]
                ])
            ;
            $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
                $data = $event->getData();
                $form = $event->getForm();

                if (!empty($data['entities'])) {
                    /** @var Id $entity */
                    foreach ($data['entities'] as $entity) {
                        $form->add('batch_'.$entity->getId(), CheckboxType::class, [
                            'value' => $entity->getId(),
                            'label' => false,
                            'required' => false
                        ]);
                    }
                }
            });
        }
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        /** @var AdminInterface $admin */
        $admin = $options['admin'];

        $view->vars['action'] = $admin->generateRouteName('batch');
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'batch_actions' => [],
                'batch_actions_label' => 'lag.admin.batch_action',
                'batch_empty' => 'lag.admin.batch_empty',
            ])
            ->setRequired('admin')
            ->setAllowedTypes('admin', AdminInterface::class)
        ;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'admin_list';
    }

    /**
     * @param MenuItemConfiguration[] $batchActions
     * @return array
     */
    protected function buildBatchSelectChoices(array $batchActions)
    {
        // in Symfony 3.0, choices are a list of values indexed by label
        $choices = [];

        foreach ($batchActions as $name => $batchAction) {
            $choices[$batchAction->getParameter('text')] = $name;
        }

        return $choices;
    }
}

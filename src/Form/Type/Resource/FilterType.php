<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Form\Type\Resource;

use LAG\AdminBundle\Metadata\Filter\FilterInterface;
use LAG\AdminBundle\Metadata\Operation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options) {
            $form = $event->getForm();

            /** @var FilterInterface $filter */
            foreach ($options['operation']->getFilters() as $filter) {
                $form->add($filter->getName(), $filter->getFormType(), array_merge([
                    'required' => false,
                ], $filter->getFormOptions()));
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'csrf_protection' => false,
                'method' => 'GET',
            ])
            ->setRequired('operation')
            ->setAllowedTypes('operation', Operation::class)
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'filter';
    }
}

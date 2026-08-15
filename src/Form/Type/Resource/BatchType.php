<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Form\Type\Resource;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BatchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $choices = [];

        foreach ($options['operations'] as $operationName) {
            $choices['lag_admin.batch.operations.'.$operationName] = $operationName;
        }

        $builder->add('operation', ChoiceType::class, [
            'choices' => $choices,
            'label' => 'lag_admin.batch.operation',
            'translation_domain' => 'admin',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'operations' => [],
            'translation_domain' => 'admin',
        ]);
        $resolver->setAllowedTypes('operations', 'array');
    }

    public function getBlockPrefix(): string
    {
        return 'lag_admin_batch';
    }
}

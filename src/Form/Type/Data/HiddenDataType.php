<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Form\Type\Data;

use LAG\AdminBundle\Resource\Factory\OperationFactoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class HiddenDataType extends AbstractType
{
    public function __construct(
        private readonly OperationFactoryInterface $operationFactory,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $operation = $this->operationFactory->create($options['operation']);

        foreach ($operation->getIdentifiers() as $identifier) {
            $builder->add($identifier, HiddenType::class);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'label' => false,
                'translation_domain' => 'lag_admin',
            ])
            ->define('operation')
            ->required()
            ->allowedTypes('string')

            ->define('empty_message')
            ->required()
            ->default('lag_admin.ui.delete_confirmation')
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'lag_admin_hidden_data';
    }
}

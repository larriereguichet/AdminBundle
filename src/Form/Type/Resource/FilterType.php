<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Form\Type\Resource;

use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Resource\Factory\OperationFactoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class FilterType extends AbstractType
{
    public function __construct(
        private readonly OperationFactoryInterface $operationFactory,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $operation = $this->operationFactory->create($options['operation']);

        if ($operation instanceof CollectionOperationInterface) {
            foreach ($operation->getFilters() as $filter) {
                $builder
                    ->add($filter->getName(), $filter->getFormType(), array_merge(['required' => false], $filter->getFormOptions()))
                ;
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->define('operation')
            ->required()
            ->allowedTypes('string')
        ;
        $resolver
            ->setDefaults([
                'csrf_protection' => false,
                'method' => 'GET',
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'lag_admin_filter';
    }
}

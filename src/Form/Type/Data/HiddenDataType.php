<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Form\Type\Data;

use LAG\AdminBundle\Resource\Registry\ResourceRegistryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class HiddenDataType extends AbstractType
{
    public function __construct(
        private readonly string $defaultApplication,
        private readonly ResourceRegistryInterface $registry,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $resource = $this->registry->get($options['resource'], $options['application']);
        $operation = $resource->getOperation($options['operation']);

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

            ->define('application')
            ->required()
            ->allowedTypes('string')
            ->default($this->defaultApplication)

            ->define('resource')
            ->required()
            ->allowedTypes('string')

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

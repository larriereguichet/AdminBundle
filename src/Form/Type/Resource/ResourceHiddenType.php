<?php

namespace LAG\AdminBundle\Form\Type\Resource;

use LAG\AdminBundle\Resource\Registry\ResourceRegistryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResourceHiddenType extends AbstractType
{
    public function __construct(
        private readonly string $defaultApplication,
        private readonly ResourceRegistryInterface $registry,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $resource = $this->registry->get($options['resource'], $options['application']);

        foreach ($resource->getIdentifiers() as $identifier) {
            $builder->add($identifier, HiddenType::class);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->define('application')
            ->required()
            ->allowedTypes('string')
            ->default($this->defaultApplication)

            ->define('resource')
            ->required()
            ->allowedTypes('string')
        ;
    }
}

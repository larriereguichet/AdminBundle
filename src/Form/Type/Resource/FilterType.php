<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Form\Type\Resource;

use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\Registry\ResourceRegistryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterType extends AbstractType
{
    public function __construct(
        private ResourceRegistryInterface $registry,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $resource = $this->registry->get($options['resource']);
        $operation = $resource->getOperation($options['operation']);

        if ($operation instanceof CollectionOperationInterface) {
            foreach ($operation->getFilters() as $filter) {
                $builder->add($filter->getName(), $filter->getFormType(), array_merge([
                    'required' => false,
                ], $filter->getFormOptions()));
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->define('resource')
            ->required()
            ->allowedTypes('string')
        ;
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
            ->addNormalizer('translation_domain', function (Options $options, $value) {
                if ($value === null) {
                    $resource = $this->registry->get($options->offsetGet('resource'));
                    $value = $resource->getTranslationDomain();
                }

                return $value;
            })
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'filter';
    }
}

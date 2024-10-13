<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Form\Type\Resource;

use LAG\AdminBundle\Form\Guesser\FormGuesserInterface;
use LAG\AdminBundle\Resource\Registry\ResourceRegistryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ResourceDataType extends AbstractType
{
    public function __construct(
        private readonly ResourceRegistryInterface $resourceRegistry,
        private readonly FormGuesserInterface $formGuesser,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $resource = $this->resourceRegistry->get($options['resource'], $options['application']);
        $operation = $resource->getOperation($options['operation']);

        foreach ($resource->getProperties() as $property) {
            if (\in_array($property->getName(), $options['exclude'])) {
                continue;
            }
            $formType = $this->formGuesser->guessFormType($operation, $property);
            $formOptions = $this->formGuesser->guessFormOptions($operation, $property);

            if ($formType === null) {
                continue;
            }
            $builder->add($property->getName(), $formType, $formOptions);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->define('exclude')
            ->default([])
            ->allowedTypes('exclude', 'array')

            ->define('application')
            ->allowedTypes('string', 'null')
            ->required()

            ->define('resource')
            ->allowedTypes('string')
            ->required()

            ->define('operation')
            ->required()
            ->allowedTypes('string')
        ;
    }
}

<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Form\Type\Resource;

use LAG\AdminBundle\Resource\Registry\ResourceRegistryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;

final class ResourceDataType extends AbstractType
{
    public function __construct(
        private readonly ResourceRegistryInterface $resourceRegistry,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options) {
            $data = $event->getData();
            $form = $event->getForm();

            if ($data === null) {
                return;
            }
            $resource = $this->resourceRegistry->get($options['resource'], $options['application']);
            $accessor = PropertyAccess::createPropertyAccessor();

            foreach ($resource->getProperties() as $property) {
                if (\in_array($property->getName(), $options['exclude'])) {
                    continue;
                }

                if (
                    \is_string($property->getPropertyPath())
                    && $property->getPropertyPath() !== '.'
                    && $accessor->isReadable($data, $property->getPropertyPath())
                    && $accessor->isWritable($data, $property->getPropertyPath())
                ) {
                    $form->add($property->getName(), null, ['property_path' => $property->getPropertyPath()]);
                }
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'exclude' => [],
            ])
            ->setAllowedTypes('exclude', 'array')

            ->define('application')
            ->allowedTypes('string', 'null')
            ->required()

            ->define('resource')
            ->allowedTypes('string', 'null')
            ->required()

            ->define('operation')
            ->allowedTypes('string', 'null')
            ->required()
        ;
    }
}

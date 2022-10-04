<?php

namespace LAG\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;

class OperationDataType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options) {
            $data = $event->getData();
            $form = $event->getForm();
            $accessor = PropertyAccess::createPropertyAccessor();
            $reflection = new \ReflectionClass($data);

            foreach ($reflection->getProperties() as $property) {
                if (in_array($property->getName(), $options['exclude'])) {
                    continue;
                }

                if ($accessor->isWritable($data, $property->getName())) {
                    $form->add($property->getName());
                }
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
               'exclude' => [],
            ])
            ->setAllowedTypes('exclude', 'array')
        ;
    }
}

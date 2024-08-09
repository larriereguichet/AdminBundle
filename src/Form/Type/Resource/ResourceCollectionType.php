<?php

namespace LAG\AdminBundle\Form\Type\Resource;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResourceCollectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData();

            if (!is_iterable($data)) {
                throw new UnexpectedTypeException($data, 'iterable');
            }

            foreach ($data as $resource) {

            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->define('entry_type')
            ->allowedTypes('string')
            ->required()

            ->define('entry_options')
            ->allowedTypes('array')
            ->default([])
        ;
    }
}

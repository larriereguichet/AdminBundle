<?php

namespace LAG\AdminBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;

class ListType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
//            ->add('actions', ChoiceType::class, [
//                'choices' => $options['actions'],
//            ])
//            ->add('selectAll', CheckboxType::class, [
//                'label' => false,
//                'required' => false,
//                'mapped' => false,
//                'attr' => [
//                    'class' => 'select-all',
//                ],
//            ])
//            ->add('entities', EntityType::class, [
//                'required' => false,
//                'class' => 'JK\CmsBundle\Entity\Article',
//                //'property' => 'id',
//                'property_path' => '[id]',
//                'label' => false,
//                'choice_label' => false,
//                'query_builder' => function(EntityRepository $repository) use ($options) {
//                    $values = [];
//                    $accessor = PropertyAccess::createPropertyAccessor();
//
//                    foreach ($options['entities'] as $entity) {
//                        $values[] = $accessor->getValue($entity, $options['entity_property']);
//                    }
//
//                    return $repository
//                        ->createQueryBuilder('entity')
//                        ->where('entity.'.$options['entity_property'].' IN (:values)')
//                        ->setParameter('values', $values)
//                    ;
//                },
//                'multiple' => true,
//                'expanded' => true,
//            ])

//            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) use ($options) {
//                $data = $event->getData();
//                $form = $event->getForm();
//
//                // we must be able to iterate on data
//                if (!is_array($data) && !$data instanceof Traversable) {
//                    return;
//                }
//                $accessor = PropertyAccess::createPropertyAccessor();
//
//                //$form->add('entities');
//
//                foreach ($data as $entity) {
//                    $value = $accessor->getValue($entity, $options['entity_property']);
//
//                    $form
//                        ->add('list_'.$value, CheckboxType::class, [
//                            'value' => $value,
//                            'label' => false,
//                            'mapped' => false,
//                            'required' => false,
//                        ])
//                    ;
//                }
//            })
//            ->addModelTransformer(new CallbackTransformer(function($value) {
//                return $value;
//            }, function($value) {
//                var_dump($value['id']);
//                die;
//
//                return $value['id'];
//            }))
        
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'entity_property' => 'id',
            ])
            ->setRequired([
                'actions',
                'entities',
            ])
            ->setAllowedTypes('entity_property', 'string')
            ->setAllowedTypes('actions', 'array')
        ;
    }
}

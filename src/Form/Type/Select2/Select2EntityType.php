<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Form\Type\Select2;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;

class Select2EntityType extends AbstractType
{
    private ManagerRegistry $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function getParent(): string
    {
        return EntityType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'allow_add' => false,
                'create_property_path' => null,
                'select2_options' => [],
            ])
            ->setAllowedTypes('allow_add', 'boolean')
            ->setAllowedTypes('create_property_path', ['string', 'null'])
            ->setAllowedTypes('select2_options', 'array')
            ->setNormalizer('create_property_path', function (Options $options, $value) {
                if ($options->offsetGet('allow_add') && !$value) {
                    throw new InvalidOptionsException('The options "create_property_path" should be defined when "allow_add" is set to true');
                }

                return $value;
            })
            ->setNormalizer('select2_options', function (Options $options, $value) {
                if ($options->offsetGet('allow_add')) {
                    $value['tags'] = json_encode(true);
                }

                return $value;
            })
        ;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($options) {
                $manager = $this->registry->getManagerForClass($options['class']);
                $repository = $manager->getRepository($options['class']);
                $propertyAccessor = PropertyAccess::createPropertyAccessor();
                $data = [];

                if (!is_iterable($event->getData())) {
                    return;
                }

                foreach ($event->getData() as $identifier) {
                    $value = $repository->find($identifier);

                    if ($value === null) {
                        $value = new $options['class']();
                        $propertyAccessor->setValue($value, $options['create_property_path'], $identifier);
                        $manager->persist($value);
                        $manager->flush();
                        $identifier = $value->getId();
                    }
                    $data[] = $identifier;
                }
                $event->setData($data);
            })
        ;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['attr']['data-controller'] = 'select2';
        $view->vars['attr']['data-options'] = json_encode($options['select2_options']);
        $view->vars['attr']['data-allow-add'] = json_encode($options['allow_add']);
    }
}

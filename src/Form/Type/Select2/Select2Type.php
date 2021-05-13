<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Form\Type\Select2;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Select2Type extends AbstractType
{
    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'select2_options' => [],
            ])
            ->setAllowedTypes('select2_options', 'array')
        ;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['attr']['data-controller'] = 'select2';
        $view->vars['attr']['data-options'] = json_encode($options['select2_options']);
    }
}

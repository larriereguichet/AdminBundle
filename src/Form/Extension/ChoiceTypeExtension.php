<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChoiceTypeExtension extends AbstractTypeExtension
{
    public static function getExtendedTypes(): iterable
    {
        return [ChoiceType::class];
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        if (!$options['select2']) {
            return;
        }
        $attr = $view->vars['attr']['data-controller'] ?? '';
        $attr .= ' lag-admin-select2';

        $view->vars['attr']['data-controller'] = trim($attr);
        $view->vars['attr']['data-allow-add'] = $options['allow_add'] ?? false;
        $view->vars['attr']['data-allow-empty'] = $options['allow_empty'] ?? false;
        $view->vars['attr']['data-multiple'] = $options['multiple'] ?? false;
        $view->vars['multiple'] = false;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->define('select2')
            ->default(false)
            ->allowedTypes('boolean')

            ->define('allow_add')
            ->default(false)
            ->allowedTypes('boolean')
        ;
    }
}

<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CollectionTypeExtension extends AbstractTypeExtension
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->define('delete_label')
            ->allowedTypes('string')
            ->default('lag_admin.ui.delete')

            ->define('add_label')
            ->allowedTypes('string')
            ->default('lag_admin.ui.add')
        ;
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['add_label'] = $options['add_label'];
        $view->vars['delete_label'] = $options['delete_label'];
    }

    public static function getExtendedTypes(): iterable
    {
        return [CollectionType::class];
    }
}

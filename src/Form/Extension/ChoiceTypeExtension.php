<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function Symfony\Component\String\u;

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
        $dataController = u($view->vars['attr']['data-controller'] ?? '');

        if (!$dataController->containsAny('select2')) {
            $view->vars['attr']['data-controller'] = $dataController->append(' select2')->trim()->toString();
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'select2' => true,
            ])
            ->setAllowedTypes('select2', 'boolean')
        ;
    }
}

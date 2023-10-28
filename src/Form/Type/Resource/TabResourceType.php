<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Form\Type\Resource;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TabResourceType extends AbstractType
{
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['tabs'] = [];

        foreach ($options['tabs'] as $tab) {
            $view->vars['tabs'][$tab] = [];
        }

        foreach ($form as $name => $child) {
            $tabName = $child->getConfig()->getOption('tab') ?? null;

            if ($tabName === null || !\array_key_exists($tabName, $view->vars['tabs'])) {
                continue; // Will be rendered by form form_end()
            }
            $view->vars['tabs'][$tabName][$name] = $child;
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'tabs' => [],
                'form_template' => '@LAGAdmin/forms/tab_form.html.twig',
            ])
            ->addAllowedTypes('tabs', ['array', 'null'])
            ->addNormalizer('tabs', function ($value) {
                if ($value === null) {
                    $value = [];
                }

                return $value;
            })
        ;
    }

    public function getParent(): string
    {
        return ResourceType::class;
    }
}

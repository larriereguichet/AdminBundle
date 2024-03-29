<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Field;

use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Display a link with button render and options.
 */
class ActionField extends AbstractField
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'template' => '@LAGAdmin/fields/action.html.twig',
                'translation' => true,
                'property_path' => null,
            ])
            ->addNormalizer('attr', function (Options $options, $value) {
                if (!empty($value['class'])) {
                    return $value;
                }
                $action = null;

                if ($options->offsetExists('action')) {
                    $action = $options->offsetGet('action');
                }

                if ('update' === $action) {
                    $value['class'] = 'btn btn-primary btn-sm';
                } elseif ('delete' === $action) {
                    $value['class'] = 'btn btn-danger btn-sm';
                } else {
                    $value['class'] = 'btn btn-secondary btn-sm';
                }

                return $value;
            })
        ;
    }
}

<?php

namespace LAG\AdminBundle\Field;

use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LinkField extends AbstractField implements ApplicationAwareInterface
{
    use ApplicationAware;

    public function getParent(): ?string
    {
        return StringField::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $appConfig = $this->getApplicationConfiguration();

        $resolver
            ->setDefaults([
                'admin' => null,
                'action' => null,
                'default' => '~',
                'icon' => null,
                'mapped' => true,
                'route' => null,
                'route_parameters' => [],
                'target' => '_self',
                'template' => '@LAGAdmin/fields/link.html.twig',
                'text' => '',
                'title' => null,
                'url' => '',
            ])
            ->setNormalizer('route', function (Options $options, $value) use ($appConfig) {
                // A route, an url or an admin should be defined
                if (!$value && !$options->offsetGet('url') && !$options->offsetGet('admin')) {
                    throw new InvalidOptionsException('Either an url or a route should be defined');
                }

                if ($options->offsetGet('admin')) {
                    $value = $appConfig->getRouteName($options->offsetGet('admin'), $options->offsetGet('action'));
                }

                return $value;
            })
            ->setNormalizer('admin', function (Options $options, $value) {
                // if a Admin is defined, an Action should be defined too
                if ($value && !$options->offsetGet('action')) {
                    throw new InvalidOptionsException('An Action should be provided if an Admin is provided');
                }

                return $value;
            })
            ->setNormalizer('text', function (Options $options, $value) {
                if ($value) {
                    return $value;
                }

                return ucfirst($options->offsetGet('route'));
            })
        ;
    }
}

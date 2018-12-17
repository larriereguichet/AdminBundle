<?php

namespace LAG\AdminBundle\Field;

use LAG\AdminBundle\Configuration\ActionConfiguration;
use LAG\AdminBundle\Field\Traits\EntityAwareTrait;
use LAG\AdminBundle\Field\Traits\TwigAwareTrait;
use LAG\AdminBundle\Routing\RoutingLoader;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;

class LinkField extends StringField implements TwigAwareFieldInterface, EntityAwareFieldInterface
{
    use TwigAwareTrait, EntityAwareTrait;

    public function isSortable(): bool
    {
        return false;
    }

    public function configureOptions(OptionsResolver $resolver, ActionConfiguration $actionConfiguration)
    {
        parent::configureOptions($resolver, $actionConfiguration);

        $resolver
            ->setDefaults([
                'template' => '@LAGAdmin/Field/link.html.twig',
                'title' => '',
                'icon' => '',
                'target' => '_self',
                'route' => '',
                'parameters' => [],
                'url' => '',
                'text' => '',
                'admin' => null,
                'action' => null,
                'class' => '',
            ])
            ->setAllowedTypes('route', 'string')
            ->setAllowedTypes('parameters', 'array')
            ->setAllowedTypes('length', 'integer')
            ->setAllowedTypes('url', 'string')
            ->setAllowedValues('target', [
                '_self',
                '_blank',
            ])
            ->setNormalizer('route', function (Options $options, $value) use ($actionConfiguration) {
                // route or url should be defined
                if (!$value && !$options->offsetGet('url') && !$options->offsetGet('admin')) {
                    throw new InvalidOptionsException(
                        'You must set either an url or a route for the property'
                    );
                }

                if ($options->offsetGet('admin')) {
                    $value = RoutingLoader::generateRouteName(
                        $options->offsetGet('admin'),
                        $options->offsetGet('action'),
                        $actionConfiguration->getAdminConfiguration()->getParameter('routing_name_pattern')
                    );
                }

                return $value;
            })
            ->setNormalizer('admin', function (Options $options, $value) {
                // if a Admin is defined, an Action should be defined too
                if ($value && !$options->offsetGet('action')) {
                    throw new InvalidOptionsException(
                        'An Action should be provided if an Admin is provided'
                    );
                }

                return $value;
            })
            ->setNormalizer('parameters', function (Options $options, $values) {
                $cleanedValues = [];

                foreach ($values as $name => $method) {
                    if (null === $method) {
                        $method = $name;
                    }
                    $cleanedValues[$name] = $method;
                }

                return $cleanedValues;
            })
        ;
    }

    public function render($value = null): string
    {
        $value = parent::render($value);
        $accessor = PropertyAccess::createPropertyAccessor();
        $options = $this->options;

        foreach ($options['parameters'] as $name => $method) {
            $options['parameters'][$name] = $accessor->getValue($this->entity, $method);
        }

        if ('' === $options['text']) {
            $options['text'] = $value;
        }

        return $this->twig->render($this->options['template'], [
            'options' => $options,
        ]);
    }
}

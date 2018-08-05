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

class ActionCollectionField extends CollectionField implements TwigAwareFieldInterface, EntityAwareFieldInterface
{
    use TwigAwareTrait, EntityAwareTrait;

    public function configureOptions(OptionsResolver $resolver, ActionConfiguration $actionConfiguration)
    {
        parent::configureOptions($resolver, $actionConfiguration);

        $actions = $actionConfiguration
            ->getAdminConfiguration()
            ->getParameter('actions')
        ;
        $defaultActions = [];

        if (key_exists('edit', $actions)) {
            $defaultActions['edit'] = $actions['edit'];
        }

        if (key_exists('delete', $actions)) {
            $defaultActions['delete'] = $actions['delete'];
        }

        $resolver
            ->setDefaults([
                'template' => '@LAGAdmin/Field/actionCollection.html.twig',
                'actions' => [],
            ])
            ->setNormalizer('actions', function (Options $options, $value) use ($actionConfiguration, $defaultActions) {
                if (!is_array($value) || 0 === count($value)) {
                    $value = $defaultActions;
                }
                $data = [];

                foreach ($value as $name => $action) {
                    $actionData = [
                        'admin' => $actionConfiguration->getAdminName(),
                        'action' => $name,
                        'title' => $actionConfiguration->getParameter('title'),
                        'text' => $actionConfiguration->getParameter('title'),
                        'parameters' => [
                            // TODO get dynamic id key
                            'id' => '',
                        ],
                    ];

                    $data[$name] = $this->resolveActionConfiguration($actionConfiguration, $actionData);
                }

                return $data;
            })
        ;
    }

    public function isSortable(): bool
    {
        return false;
    }

    public function render($value = null): string
    {
        $accessor = PropertyAccess::createPropertyAccessor();

        if (key_exists('edit', $this->options['actions'])) {
            $this->options['actions']['edit']['parameters']['id'] = $accessor->getValue($this->entity, 'id');
        }

        if (key_exists('delete', $this->options['actions'])) {
            $this->options['actions']['delete']['parameters']['id'] = $accessor->getValue($this->entity, 'id');
        }

        return $this->twig->render($this->options['template'], [
            'actions' => $this->options['actions'],
        ]);
    }

    protected function resolveActionConfiguration(ActionConfiguration $actionConfiguration, $value)
    {
        $resolver = new OptionsResolver();
        $resolver
            ->setDefaults([
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

        return $resolver->resolve($value);
    }
}

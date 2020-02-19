<?php

namespace LAG\AdminBundle\Configuration;

use JK\Configuration\Configuration;
use LAG\Component\StringUtils\StringUtils;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MenuItemConfiguration extends Configuration
{
    /**
     * @var string
     */
    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;

        parent::__construct();
    }

    /**
     * Define allowed parameters and values for this configuration, using optionsResolver component.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        // user can defined an admin name
        $resolver
            ->setDefaults([
                'admin' => null,
                'action' => null,
                'route' => null,
                'route_parameters' => [],
                'uri' => null,
                'attributes' => [],
                'linkAttributes' => [
                    'class' => 'list-group-item list-group-item-action',
                ],
                'text' => null,
                'children' => [],
            ])
            ->setNormalizer('admin', function (Options $options, $adminName) {
                // user has to defined either an admin name and an action name, or a route name with optional
                // parameters, or an url
                if (null === $adminName
                    && null === $options->offsetGet('route')
                    && null === $options->offsetGet('uri')
                ) {
                    throw new InvalidOptionsException('You should either defined an admin name, or route name or an uri');
                }

                return $adminName;
            })
            // if an admin name is set, an action name can provided. This action will be the menu link
            ->setNormalizer('action', function (Options $options, $action) {
                // if an action name is provided, an admin name should be defined too
                if (null !== $action && null === $options->offsetGet('admin')) {
                    throw new InvalidOptionsException('You should provide an admin name for this action '.$action);
                }

                // default to list action
                if (null !== $options->offsetGet('admin') && null === $action) {
                    $action = 'list';
                }

                return $action;
            })
            ->setNormalizer('children', function (Options $options, $items) {
                if (!is_array($items)) {
                    $items = [];
                }
                $resolver = new OptionsResolver();
                $resolvedItems = [];

                foreach ($items as $name => $item) {
                    $itemConfiguration = new MenuItemConfiguration($name);
                    $itemConfiguration->configureOptions($resolver);
                    $itemConfiguration->setParameters($resolver->resolve($item));

                    $resolvedItems[$name] = $itemConfiguration;
                }

                return $resolvedItems;
            })
            ->setNormalizer('text', function (Options $options, $text) {
                if ($text) {
                    return $text;
                }

                if ($options->offsetGet('admin')) {
                    $text = ucfirst($options->offsetGet('admin'));

                    if (StringUtils::endsWith($text, 'y')) {
                        $text = substr($text, 0, strlen($text) - 1).'ie';
                    }

                    if (!StringUtils::endsWith($text, 's')) {
                        $text .= 's';
                    }

                    return $text;
                }

                return ucfirst($this->name);
            })
        ;
    }

    public function all()
    {
        $values = parent::all();

        /** @var MenuItemConfiguration $value */
        foreach ($values['children'] as $name => $value) {
            $values['children'][$name] = $value->all();
        }

        return $values;
    }

    public function getName(): string
    {
        return $this->name;
    }
}

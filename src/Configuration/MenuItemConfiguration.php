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
     * @var ?string
     */
    private $position;

    /**
     * @var string
     */
    private $name;

    /**
     * MenuItemConfiguration constructor.
     *
     * @param string $position
     */
    public function __construct(string $name, ?string $position)
    {
        $this->position = $position;
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
            ->setDefault('admin', null)
            ->setDefault('action', null)
            ->setDefault('route', null)
            ->setDefault('url', null)
            ->setDefault('parameters', [])
            ->setDefault('text', '')
            ->setDefault('attr', [])
            ->setDefault('link_attr', [
                'class' => 'nav-link',
            ])
            ->setDefault('items', [])
            ->setDefault('icon', null)
            ->setDefault('link_css_class', 'nav-link')
            ->setNormalizer('admin', function (Options $options, $adminName) {
                // user has to defined either an admin name and an action name, or a route name with optional
                // parameters, or an url
                if (null === $adminName
                    && null === $options->offsetGet('route')
                    && null === $options->offsetGet('url')
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
            ->setAllowedTypes('parameters', 'array')
            ->setNormalizer('attr', function (Options $options, $attr) {
                if (!is_array($attr)) {
                    $attr = [];
                }

                if (empty($attr['id'])) {
                    $attr['id'] = uniqid('admin-menu-');
                }

                if ('horizontal' === $this->position && !key_exists('class', $attr)) {
                    $attr['class'] = 'nav-item';
                }

                return $attr;
            })
            ->setNormalizer('link_attr', function (Options $options, $value) {
                if (!is_array($value)) {
                    $value = [];
                }

                if (!key_exists('class', $value)) {
                    $value['class'] = 'nav-link';
                }

                return $value;
            })
            ->setNormalizer('items', function (Options $options, $items) {
                if (!is_array($items)) {
                    $items = [];
                }
                $resolver = new OptionsResolver();
                $resolvedItems = [];

                foreach ($items as $name => $item) {
                    $itemConfiguration = new MenuItemConfiguration($name, $this->position);
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
}

<?php

namespace LAG\AdminBundle\Configuration;

use JK\Configuration\Configuration;
use LAG\AdminBundle\Routing\Resolver\RoutingResolverInterface;
use LAG\Component\StringUtils\StringUtils;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class MenuItemConfiguration extends Configuration
{
    /**
     * @var string
     */
    private $itemName;

    /**
     * @var RoutingResolverInterface
     */
    private $routingResolver;

    /**
     * @var string
     */
    private $menuName;

    /**
     * @var string
     */
    private $adminName;

    /**
     * @var mixed
     */
    private $data;

    public function __construct(
        string $itemName,
        string $menuName,
        string $adminName,
        RoutingResolverInterface $routingResolver,
        $data = null
    ) {
        $this->itemName = $itemName;
        $this->menuName = $menuName;
        $this->adminName = $adminName;
        $this->routingResolver = $routingResolver;
        $this->data = $data;
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
                'admin' => $this->adminName,
                'action' => null,
                'route' => null,
                'routeParameters' => [],
                'uri' => null,
                'attributes' => [],
                'linkAttributes' => [],
                'label' => null,
                'icon' => null,
                'text' => null,
                'children' => [],
                //'allow_safe_labels' => true,
                'extras' => ['safe_label' => true],
            ])
            ->setNormalizer('route', function (Options $options, $route) {
                if ($options->offsetGet('uri')) {
                    return $route;
                }

                if (!$route) {
                    $route = $this->routingResolver->resolve($options->offsetGet('admin'), $options->offsetGet('action'));
                }

                return $route;
            })
            ->setNormalizer('routeParameters', function (Options $options, $routeParameters) {
                if ($routeParameters === null) {
                    return [];
                }

                if (!$this->data) {
                    return $routeParameters;
                }
                $accessor = new PropertyAccessor(true);

                foreach ($routeParameters as $name => $value) {
                    $hasDoubleDash = '__' === substr($value, 0, 2);

                    if (null === $value) {
                        $value = $name;
                    }

                    if ($this->data && $accessor->isReadable($this->data, $value) && !$hasDoubleDash) {
                        $routeParameters[$name] = $accessor->getValue($this->data, $value);
                    } elseif ($hasDoubleDash) {
                        $routeParameters[$name] = substr($value, 2);
                    } else {
                        $routeParameters[$name] = $value;
                    }
                }

                return $routeParameters;
            })
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
                if ($action !== null && $options->offsetGet('admin') === null) {
                    throw new InvalidOptionsException('You should provide an admin name for this action '.$action);
                }

                // default to list action
                if ($options->offsetGet('admin') !== null && $action === null) {
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
                    $itemConfiguration = new MenuItemConfiguration($name, $this->menuName, $this->adminName, $this->routingResolver, $this->data);
                    $itemConfiguration->configureOptions($resolver);
                    $itemConfiguration->setParameters($resolver->resolve($item));
                    $resolver->clear();

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

                return $text;
            })
            ->setNormalizer('linkAttributes', function (Options $options, $linkAttributes) {
                if (!$linkAttributes) {
                    $linkAttributes = [];
                }

                if ('left' === $this->menuName && empty($linkAttributes)) {
                    $linkAttributes = ['class' => 'list-group-item list-group-item-action'];
                }

                return $linkAttributes;
            })
        ;
    }
}

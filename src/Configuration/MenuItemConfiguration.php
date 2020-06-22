<?php

namespace LAG\AdminBundle\Configuration;

use JK\Configuration\Configuration;
use LAG\AdminBundle\Routing\Resolver\RoutingResolverInterface;
use LAG\Component\StringUtils\StringUtils;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

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

    public function __construct(
        string $itemName,
        string $menuName,
        RoutingResolverInterface $routingResolver,
        ?string $adminName = null
    ) {
        $this->itemName = $itemName;
        $this->routingResolver = $routingResolver;
        $this->menuName = $menuName;
        $this->adminName = $adminName;
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
                    $itemConfiguration = new MenuItemConfiguration($name, $this->menuName, $this->routingResolver, $this->adminName);
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

                if ($this->menuName === 'left' && empty($linkAttributes)) {
                    $linkAttributes = ['class' => 'list-group-item list-group-item-action'];
                }

                return $linkAttributes;
            })
        ;
    }
}

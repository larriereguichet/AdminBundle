<?php

namespace LAG\AdminBundle\Configuration;

use JK\Configuration\Configuration;
use LAG\AdminBundle\Routing\Resolver\RoutingResolverInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MenuConfiguration extends Configuration
{
    /**
     * @var string
     */
    private $menuName;

    /**
     * @var RoutingResolverInterface
     */
    private $routingResolver;

    /**
     * @var string
     */
    private $adminName;

    /**
     * MenuConfiguration constructor.
     */
    public function __construct(string $menuItemName, RoutingResolverInterface $routingRoutingResolver, ?string $adminName = null)
    {
        $this->menuName = $menuItemName;
        $this->routingResolver = $routingRoutingResolver;
        $this->adminName = $adminName;
        parent::__construct();
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'children' => [],
                'attributes' => [
                    'class' => 'list-group admin admin-menu-'.$this->menuName,
                ],
                'inherits' => true,
            ])
            ->setAllowedTypes('inherits', 'boolean')
            ->setNormalizer('children', function (Options $options, $value) {
                if (!is_array($value)) {
                    $value = [];
                }
                $innerResolver = new OptionsResolver();

                foreach ($value as $name => $item) {
                    if (!$item) {
                        $item = [];
                    }
                    $configuration = new MenuItemConfiguration($name, $this->menuName, $this->routingResolver, $this->adminName);
                    $configuration->configureOptions($innerResolver);
                    $value[$name] = $innerResolver->resolve($item);
                    $innerResolver->clear();
                }

                return $value;
            })
        ;
    }

    public function all()
    {
        $values = parent::all();

        foreach ($values['children'] as $name => $value) {
            $values['children'][$name] = $value;
        }

        return $values;
    }

    public function getMenuName(): string
    {
        return $this->menuName;
    }

    public function getRoute(): string
    {
        return $this->parameters->get('route');
    }
}

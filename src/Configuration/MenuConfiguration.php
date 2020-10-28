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
     * @var mixed
     */
    private $data;

    /**
     * MenuConfiguration constructor.
     */
    public function __construct(string $menuItemName, string $adminName, RoutingResolverInterface $routingRoutingResolver, $data = null)
    {
        $this->menuName = $menuItemName;
        $this->routingResolver = $routingRoutingResolver;
        $this->data = $data;
        parent::__construct();
        $this->adminName = $adminName;
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
                    $configuration = new MenuItemConfiguration($name, $this->menuName, $this->adminName, $this->routingResolver, $this->data);
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

<?php

namespace LAG\AdminBundle\Menu\Provider;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Knp\Menu\Provider\MenuProviderInterface;
use LAG\AdminBundle\Factory\ConfigurationFactory;
use LAG\AdminBundle\Routing\Resolver\RoutingResolverInterface;

class MenuProvider implements MenuProviderInterface
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var ConfigurationFactory
     */
    private $configurationFactory;

    /**
     * @var RoutingResolverInterface
     */
    private $resolver;

    /**
     * @var array
     */
    private $menuConfigurations;

    public function __construct(
        array $menuConfigurations,
        FactoryInterface $factory,
        ConfigurationFactory $configurationFactory,
        RoutingResolverInterface $resolver
    ) {
        $this->factory = $factory;
        $this->configurationFactory = $configurationFactory;
        $this->resolver = $resolver;
        $this->menuConfigurations = $menuConfigurations;
    }

    public function get(string $name, array $options = []): ItemInterface
    {
        $menuConfiguration = $this->configurationFactory->createMenuConfiguration($name, []);
        $menuConfiguration = $menuConfiguration->all();

        $menu = $this->factory->createItem('root', [
            'attributes' => $menuConfiguration['attributes'],
        ]);

        foreach ($menuConfiguration['children'] as $itemConfiguration) {
            $menu->addChild($itemConfiguration['text'], [
                'attributes' => $itemConfiguration['attributes'],
                'linkAttributes' => $itemConfiguration['linkAttributes'],
                'route' => $this->resolver->resolveOptions($itemConfiguration),
            ]);
        }

        return $menu;
    }

    public function has(string $name, array $options = []): bool
    {
        return key_exists($name, $this->menuConfigurations);
    }

    /**
     * @return ItemInterface[]
     */
    public function all(): array
    {
        $menus = [];

        foreach ($this->menuConfigurations as $menuName => $menuConfiguration) {
            if (!is_array($menuConfiguration)) {
                $menuConfiguration = [];
            }
            $menus[$menuName] = $this->get($menuName, $menuConfiguration);
        }

        return $menus;
    }
}

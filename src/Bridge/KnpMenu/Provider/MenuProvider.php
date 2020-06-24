<?php

namespace LAG\AdminBundle\Bridge\KnpMenu\Provider;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Knp\Menu\Provider\MenuProviderInterface;
use LAG\AdminBundle\Factory\ConfigurationFactory;
use Symfony\Component\HttpFoundation\RequestStack;

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
     * @var array
     */
    private $menuConfigurations;

    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(
        array $menuConfigurations,
        FactoryInterface $factory,
        ConfigurationFactory $configurationFactory,
        RequestStack $requestStack
    ) {
        $this->factory = $factory;
        $this->configurationFactory = $configurationFactory;
        $this->menuConfigurations = $menuConfigurations;
        $this->requestStack = $requestStack;
    }

    public function get(string $name, array $options = []): ItemInterface
    {
        $menuConfiguration = $this->configurationFactory->createMenuConfiguration($name, $options)->all();
        $menu = $this->factory->createItem('root', [
            'attributes' => $menuConfiguration['attributes'],
        ]);
        $currentRoute = $this->requestStack->getMasterRequest()->get('_route');

        foreach ($menuConfiguration['children'] as $itemConfiguration) {
            $child = $menu->addChild($itemConfiguration['text'], $itemConfiguration);

            if (!empty($itemConfiguration['icon'])) {
                $child->setExtra('icon', $itemConfiguration['icon']);
            }

            if (!empty($itemConfiguration['route']) && $itemConfiguration['route'] === $currentRoute) {
                $class = $child->setCurrent(true)->getAttribute('class');
                $child->setAttribute('class', $class.' current');
            }
            // TODO move in the bootstrap theme
            $class = $child->getAttribute('class');
            $child->setAttribute('class', $class.' nav-item');

            $linkClass = $child->getLinkAttribute('class');
            $child->setLinkAttribute('class', $linkClass.' nav-link');
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

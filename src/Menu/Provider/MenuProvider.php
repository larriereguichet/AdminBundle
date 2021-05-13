<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Menu\Provider;

use Knp\Menu\ItemInterface;
use Knp\Menu\Provider\MenuProviderInterface;
use LAG\AdminBundle\Menu\Factory\MenuFactoryInterface;

/**
 * Create a new KNP menu using the MenuConfiguration class to validate provided options.
 */
class MenuProvider implements MenuProviderInterface
{
    private array $menuConfigurations;
    private MenuFactoryInterface $menuFactory;

    public function __construct(
        array $menuConfigurations,
        MenuFactoryInterface $menuFactory
    ) {
        $this->menuConfigurations = $menuConfigurations;
        $this->menuFactory = $menuFactory;
    }

    public function get(string $name, array $options = []): ItemInterface
    {
        $options = array_merge($this->menuConfigurations[$name] ?? [], $options);

        return $this->menuFactory->create($name, $options);
    }

    public function has(string $name, array $options = []): bool
    {
        return \array_key_exists($name, $this->menuConfigurations);
    }
}

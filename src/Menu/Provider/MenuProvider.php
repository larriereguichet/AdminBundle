<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Menu\Provider;

use Knp\Menu\ItemInterface;
use Knp\Menu\Provider\MenuProviderInterface;
use LAG\AdminBundle\Factory\Configuration\ConfigurationFactoryInterface;
use LAG\AdminBundle\Menu\Factory\MenuFactoryInterface;
use Symfony\Component\Security\Core\Security;

/**
 * Create a new KNP menu using the MenuConfiguration class to validate provided options.
 */
class MenuProvider implements MenuProviderInterface
{
    private array $menuConfigurations;
    private MenuFactoryInterface $menuFactory;
    private ConfigurationFactoryInterface $configurationFactory;
    private Security $security;

    public function __construct(
        array $menuConfigurations,
        MenuFactoryInterface $menuFactory,
        ConfigurationFactoryInterface $configurationFactory,
        Security $security
    ) {
        $this->configurationFactory = $configurationFactory;
        $this->menuConfigurations = $menuConfigurations;
        $this->security = $security;
        $this->menuFactory = $menuFactory;
    }

    public function get(string $name, array $options = []): ItemInterface
    {
        return $this->menuFactory->create($name, $options);
    }

    public function has(string $name, array $options = []): bool
    {
        if (!\array_key_exists($name, $this->menuConfigurations)) {
            return false;
        }
        $configuration = $this->configurationFactory->createMenuConfiguration($name, $options);

        if (!$configuration->hasPermissions()) {
            return true;
        }

        foreach ($configuration->getPermissions() as $permission) {
            if (!$this->security->isGranted($permission, $this->security->getUser())) {
                return false;
            }
        }

        return true;
    }
}

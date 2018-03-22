<?php

namespace LAG\AdminBundle\Event\Subscriber;

use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Configuration\ApplicationConfigurationStorage;
use LAG\AdminBundle\Event\AdminEvents;
use LAG\AdminBundle\Event\MenuEvent;
use LAG\AdminBundle\Factory\ConfigurationFactory;
use LAG\AdminBundle\Factory\MenuFactory;
use LAG\AdminBundle\Resource\ResourceCollection;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MenuSubscriber implements EventSubscriberInterface
{
    /**
     * @var ApplicationConfiguration
     */
    private $applicationConfiguration;

    /**
     * @var MenuFactory
     */
    private $menuFactory;

    /**
     * @var ResourceCollection
     */
    private $resourceCollection;

    /**
     * @var ConfigurationFactory
     */
    private $configurationFactory;

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            AdminEvents::MENU => 'buildMenus',
        ];
    }

    /**
     * MenuSubscriber constructor.
     *
     * @param ApplicationConfigurationStorage $storage
     * @param MenuFactory                     $menuFactory
     * @param ConfigurationFactory            $configurationFactory
     * @param ResourceCollection              $resourceCollection
     */
    public function __construct(
        ApplicationConfigurationStorage $storage,
        MenuFactory $menuFactory,
        ConfigurationFactory $configurationFactory,
        ResourceCollection $resourceCollection
    ) {
        $this->applicationConfiguration = $storage->getConfiguration();
        $this->menuFactory = $menuFactory;
        $this->resourceCollection = $resourceCollection;
        $this->configurationFactory = $configurationFactory;
    }

    /**
     * Build menus according to the given configuration.
     *
     * @param MenuEvent $event
     */
    public function buildMenus(MenuEvent $event)
    {
        if (!$this->applicationConfiguration->getParameter('enable_menus')) {
            return;
        }
        $menuConfigurations = [];

        if ($event->isBuildResourceMenu()) {
            $menuConfigurations['left'] = $this->configurationFactory->createResourceMenuConfiguration();
        }
        $menuConfigurations = array_merge($menuConfigurations, $event->getMenuConfigurations());

        foreach ($menuConfigurations as $name => $menuConfiguration) {
            if (!$this->menuFactory->hasMenu($name)) {
                $this->menuFactory->create($name, $menuConfiguration);
            } else {
                $menu = $this->menuFactory->getMenu($name);

                foreach ($menuConfiguration['items'] as $itemConfiguration) {
                    $menuItem = $this->menuFactory->createMenuItem($itemConfiguration);
                    $menu->addItem($menuItem);
                }
            }
        }
    }
}

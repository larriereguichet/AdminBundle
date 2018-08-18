<?php

namespace LAG\AdminBundle\Event\Subscriber;

use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Configuration\ApplicationConfigurationStorage;
use LAG\AdminBundle\Event\AdminEvents;
use LAG\AdminBundle\Event\Menu\MenuConfigurationEvent;
use LAG\AdminBundle\Event\MenuEvent;
use LAG\AdminBundle\Factory\ConfigurationFactory;
use LAG\AdminBundle\Factory\MenuFactory;
use LAG\AdminBundle\Resource\ResourceCollection;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
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
     * @var array
     */
    private $adminMenuConfigurations;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

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
     * @param EventDispatcherInterface        $eventDispatcher
     * @param array                           $adminMenuConfigurations
     */
    public function __construct(
        ApplicationConfigurationStorage $storage,
        MenuFactory $menuFactory,
        ConfigurationFactory $configurationFactory,
        ResourceCollection $resourceCollection,
        EventDispatcherInterface $eventDispatcher,
        array $adminMenuConfigurations = []
    ) {
        $this->applicationConfiguration = $storage->getConfiguration();
        $this->menuFactory = $menuFactory;
        $this->resourceCollection = $resourceCollection;
        $this->configurationFactory = $configurationFactory;
        $this->adminMenuConfigurations = $adminMenuConfigurations;
        $this->eventDispatcher = $eventDispatcher;
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
        $menuConfigurations = array_merge_recursive(
            $this->adminMenuConfigurations,
            $event->getMenuConfigurations()
        );
        $configurationEvent = new MenuConfigurationEvent($menuConfigurations);

        // Dispatch a pre-menu build event to allow dynamic configuration modifications
        $this
            ->eventDispatcher
            ->dispatch(AdminEvents::MENU_CONFIGURATION, $configurationEvent)
        ;
        $menuConfigurations = $configurationEvent->getMenuConfigurations();

        foreach ($menuConfigurations as $name => $menuConfiguration) {
            $this->menuFactory->create($name, $menuConfiguration);
        }
    }
}

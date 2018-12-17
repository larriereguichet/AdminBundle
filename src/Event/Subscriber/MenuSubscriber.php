<?php

namespace LAG\AdminBundle\Event\Subscriber;

use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Configuration\ApplicationConfigurationStorage;
use LAG\AdminBundle\Event\Events;
use LAG\AdminBundle\Event\Menu\MenuConfigurationEvent;
use LAG\AdminBundle\Event\Events\MenuEvent;
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
            Events::MENU => 'buildMenus',
        ];
    }

    /**
     * MenuSubscriber constructor.
     *
     * @param ApplicationConfigurationStorage $storage
     * @param MenuFactory                     $menuFactory
     * @param EventDispatcherInterface        $eventDispatcher
     * @param array                           $adminMenuConfigurations
     */
    public function __construct(
        ApplicationConfigurationStorage $storage,
        MenuFactory $menuFactory,
        EventDispatcherInterface $eventDispatcher,
        array $adminMenuConfigurations = []
    ) {
        $this->applicationConfiguration = $storage->getConfiguration();
        $this->menuFactory = $menuFactory;
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
            ->dispatch(Events::MENU_CONFIGURATION, $configurationEvent)
        ;
        $menuConfigurations = $configurationEvent->getMenuConfigurations();

        foreach ($menuConfigurations as $name => $menuConfiguration) {
            $this->menuFactory->create($name, $menuConfiguration);
        }
    }
}

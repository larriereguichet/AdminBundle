<?php

namespace LAG\AdminBundle\Tests\Event\Subscriber;

use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Configuration\ApplicationConfigurationStorage;
use LAG\AdminBundle\Event\Events;
use LAG\AdminBundle\Event\Events\MenuEvent;
use LAG\AdminBundle\Event\Menu\MenuConfigurationEvent;
use LAG\AdminBundle\Event\Subscriber\MenuSubscriber;
use LAG\AdminBundle\Factory\MenuFactory;
use LAG\AdminBundle\Tests\AdminTestBase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class MenuSubscriberTest extends AdminTestBase
{
    public function testBuildMenu()
    {
        list(, $storage, $menuFactory, $eventDispatcher) = $this->createSubscriber();
        $event = new MenuEvent();

        $applicationConfiguration = $this->createMock(ApplicationConfiguration::class);
        $applicationConfiguration
            ->expects($this->once())
            ->method('getParameter')
            ->willReturnMap([
                ['enable_menus', true],
            ])
        ;
        $storage
            ->expects($this->once())
            ->method('getConfiguration')
            ->willReturn($applicationConfiguration)
        ;

        $eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with(Events::MENU_CONFIGURATION, new MenuConfigurationEvent([
                'my_little_menu' => [],
            ]))
        ;
        $subscriber = new MenuSubscriber($storage, $menuFactory, $eventDispatcher, [
            'my_little_menu' => [],
        ]);

        $menuFactory
            ->expects($this->once())
            ->method('create')
            ->with('my_little_menu', [])
        ;

        $subscriber->buildMenus($event);
    }

    public function testBuildMenuWithoutConfiguration()
    {
        list($subscriber, , $menuFactory, $eventDispatcher) = $this->createSubscriber();
        $event = new MenuEvent();

        $menuFactory
            ->expects($this->never())
            ->method('create')
        ;
        $eventDispatcher
            ->expects($this->never())
            ->method('dispatch')
        ;

        $subscriber->buildMenus($event);
    }

    /**
     * @param array $adminMenuConfigurations
     *
     * @return MockObject[]|MenuSubscriber[]
     */
    private function createSubscriber(array $adminMenuConfigurations = [])
    {
        $storage = $this->createMock(ApplicationConfigurationStorage::class);
        $menuFactory = $this->createMock(MenuFactory::class);
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $subscriber = new MenuSubscriber(
            $storage,
            $menuFactory,
            $eventDispatcher,
            $adminMenuConfigurations
        );

        return [
            $subscriber,
            $storage,
            $menuFactory,
            $eventDispatcher,
        ];
    }

}

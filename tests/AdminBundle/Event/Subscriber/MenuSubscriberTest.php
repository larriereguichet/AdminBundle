<?php

namespace LAG\AdminBundle\Tests\Event\Subscriber;

use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Configuration\ApplicationConfigurationStorage;
use LAG\AdminBundle\Event\Events;
use LAG\AdminBundle\Event\Events\BuildMenuEvent;
use LAG\AdminBundle\Event\Menu\MenuConfigurationEvent;
use LAG\AdminBundle\Event\Subscriber\MenuSubscriber;
use LAG\AdminBundle\Factory\MenuFactory;
use LAG\AdminBundle\Tests\AdminTestBase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class MenuSubscriberTest extends AdminTestBase
{
    public function testServiceExists()
    {
        $this->assertServiceExists(MenuSubscriber::class);
    }

    public function testSubscribedEvents()
    {
        $this->assertEquals([
            Events::MENU => 'buildMenus',
        ], MenuSubscriber::getSubscribedEvents());
    }

    public function testBuildMenu()
    {
        list($subscriber, , $menuFactory, $eventDispatcher) = $this->createSubscriber([
            'my_little_menu' => [],
        ], [
            ['enable_menus', true],
        ]);

        $this->assertSubscribedMethodsExists($subscriber);
        $event = new BuildMenuEvent();

        $eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with(new MenuConfigurationEvent([
                'my_little_menu' => [],
            ]), Events::MENU_CONFIGURATION)
        ;
        $menuFactory
            ->expects($this->once())
            ->method('create')
            ->with('my_little_menu', [])
        ;
        $subscriber->buildMenus($event);
    }

    public function testBuildMenuWithoutConfiguration()
    {
        list($subscriber,, $menuFactory, $eventDispatcher) = $this->createSubscriber();
        $event = new BuildMenuEvent();

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
     * @return MockObject[]|MenuSubscriber[]|EventSubscriberInterface
     */
    private function createSubscriber(array $adminMenuConfigurations = [], array $applicationConfigurationMap = [])
    {
        $storage = $this->createMock(ApplicationConfigurationStorage::class);
        $menuFactory = $this->createMock(MenuFactory::class);
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $applicationConfiguration = $this->createMock(ApplicationConfiguration::class);
        $applicationConfiguration
            ->expects($this->once())
            ->method('getParameter')
            ->willReturnMap($applicationConfigurationMap)
        ;
        $storage
            ->expects($this->once())
            ->method('getConfiguration')
            ->willReturn($applicationConfiguration)
        ;

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

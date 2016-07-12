<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Event\Subscriber;

use LAG\AdminBundle\Admin\Event\AdminEvent;
use LAG\AdminBundle\Admin\Event\AdminEvents;
use LAG\AdminBundle\Event\Subscriber\ExtraConfigurationSubscriber;
use LAG\AdminBundle\Tests\AdminTestBase;
use PHPUnit_Framework_MockObject_MockObject;

class ExtraConfigurationSubscriberTest extends AdminTestBase
{
    /**
     * ExtraConfigurationSubscriber SHOULD subscribe to the Admin creation and the Action creation event
     */
    public function testGetSubscribedEvents()
    {
        $subscribedEvents = ExtraConfigurationSubscriber::getSubscribedEvents();

        $this->assertArrayHasKey(AdminEvents::ADMIN_CREATE, $subscribedEvents);
        $this->assertArrayHasKey(AdminEvents::ACTION_CREATE, $subscribedEvents);
        $this->assertContains('actionCreate', $subscribedEvents);
        $this->assertContains('adminCreate', $subscribedEvents);
    }

    /**
     * AdminCreate method SHOULD add the CRUD actions only if required
     */
    public function testAdminCreate()
    {
        // with extra configuration disabled, adminCreate method SHOULD not modifiy the configuration
        $subscriber = new ExtraConfigurationSubscriber(
            false,
            $this->mockDoctrine(),
            $this->createConfigurationFactory()
        );
        $event = new AdminEvent();
        $event->setAdminConfiguration([]);
        $subscriber->adminCreate($event);
        $this->assertEquals([], $event->getAdminConfiguration());

        // with extra configuration enabled, adminCreate method SHOULD fill action configuration if it is empty
        $subscriber = new ExtraConfigurationSubscriber(
            true,
            $this->mockDoctrine(),
            $this->createConfigurationFactory()
        );
        $event = new AdminEvent();
        $event->setAdminConfiguration([]);
        $subscriber->adminCreate($event);
        $this->assertEquals([
            'actions' => [
                'create' => [],
                'list' => [],
                'edit' => [],
                'delete' => [],
                'batch' => [],
            ]
        ], $event->getAdminConfiguration());

        // adminCreate method SHOULD not modified actual configuration
        $event = new AdminEvent();
        $event->setAdminConfiguration([
            'actions' => [
                'myAction' => []
            ]
        ]);
        $subscriber->adminCreate($event);
        $this->assertEquals([
            'actions' => [
                'myAction' => []
            ]
        ], $event->getAdminConfiguration());
    }

    public function testConfigurationDisabled()
    {
        $subscriber = new ExtraConfigurationSubscriber(
            false,
            $this->mockDoctrine(),
            $this->createConfigurationFactory()
        );
        /** @var AdminEvent|PHPUnit_Framework_MockObject_MockObject $adminEvent */
        $adminEvent = $this
            ->getMockBuilder(AdminEvent::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $adminEvent
            ->expects($this->never())
            ->method('getAdminConfiguration')
        ;

        // no method should be called
        $subscriber->adminCreate($adminEvent);
    }

    public function testMenuConfiguration()
    {
        $subscriber = new ExtraConfigurationSubscriber(
            true,
            $this->mockDoctrine(),
            $this->createConfigurationFactory()
        );
        $adminEvent = new AdminEvent();
        $adminEvent->setActionName('list');
        $adminEvent->setAdmin($this->createAdmin('test', [
            'entity' => 'test',
            'form' => 'test',
        ]));
        $adminEvent->setActionConfiguration([
            'fields' => [
                'test' => []
            ]
        ]);
        $subscriber->actionCreate($adminEvent);

        $configuration = $adminEvent->getActionConfiguration();

        $this->assertArrayHasKey('menus', $configuration);
        $this->assertArrayHasKey('top', $configuration['menus']);
        $this->assertArrayHasKey('items', $configuration['menus']['top']);
        $this->assertArrayHasKey('create', $configuration['menus']['top']['items']);
        $this->assertEquals([
            'admin' => 'test',
            'action' => 'create',
            'icon' => 'fa fa-plus',
        ], $configuration['menus']['top']['items']['create']);
    }
}

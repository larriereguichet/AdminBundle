<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Event\Subscriber;

use LAG\AdminBundle\Action\Event\ActionCreateEvent;
use LAG\AdminBundle\Action\Event\ActionEvents;
use LAG\AdminBundle\Admin\Event\AdminCreateEvent;
use LAG\AdminBundle\Admin\Event\AdminEvents;
use LAG\AdminBundle\Event\Subscriber\ExtraConfigurationSubscriber;
use LAG\AdminBundle\Tests\AdminTestBase;
use PHPUnit_Framework_MockObject_MockObject;

class ExtraConfigurationSubscriberTest extends AdminTestBase
{
    /**
     * ExtraConfigurationSubscriber SHOULD subscribe to the Admin creation and the Action creation event.
     */
    public function testGetSubscribedEvents()
    {
        $subscribedEvents = ExtraConfigurationSubscriber::getSubscribedEvents();

        $this->assertArrayHasKey(AdminEvents::ADMIN_CREATE, $subscribedEvents);
        $this->assertArrayHasKey(ActionEvents::ACTION_CREATE, $subscribedEvents);
        $this->assertContains('actionCreate', $subscribedEvents);
        $this->assertContains('adminCreate', $subscribedEvents);
    }

    /**
     * with extra configuration disabled, adminCreate method SHOULD not modify the configuration.
     */
    public function testAdminCreateWithoutExtraConfiguration()
    {
        $subscriber = new ExtraConfigurationSubscriber(
            false,
            $this->mockDoctrine(),
            $this->createConfigurationFactory()
        );
        $event = new AdminCreateEvent('test', [
            'my_config'
        ]);
        $subscriber->adminCreate($event);
        $this->assertEquals([
            'my_config'
        ], $event->getAdminConfiguration());
    }

    /**
     * with extra configuration enabled, adminCreate method SHOULD fill action configuration if it is empty.
     */
    public function testAdminCreateWithExtraConfiguration()
    {
        $subscriber = new ExtraConfigurationSubscriber(
            true,
            $this->mockDoctrine(),
            $this->createConfigurationFactory()
        );
        $event = new AdminCreateEvent('my_admin', []);
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
    }

    /**
     * adminCreate method SHOULD not modified actual configuration.
     */
    public function testAdminCreate()
    {
        $subscriber = new ExtraConfigurationSubscriber(
            true,
            $this->mockDoctrine(),
            $this->createConfigurationFactory()
        );
        $event = new AdminCreateEvent('my_admin', [
            'actions' => [
                'myAction' => []
            ],
            'an_other_key' => 'some value'
        ]);
        $subscriber->adminCreate($event);
        $this->assertEquals([
            'actions' => [
                'myAction' => []
            ],
            'an_other_key' => 'some value'
        ], $event->getAdminConfiguration());
    }

    public function testConfigurationDisabled()
    {
        $subscriber = new ExtraConfigurationSubscriber(
            false,
            $this->mockDoctrine(),
            $this->createConfigurationFactory()
        );
        /** @var AdminCreateEvent|PHPUnit_Framework_MockObject_MockObject $adminEvent */
        $adminEvent = $this
            ->getMockBuilder(AdminCreateEvent::class)
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
        $adminEvent = new ActionCreateEvent(
            'list',
            [
                'fields' => [
                    'test' => []
                ]
            ],
            $this->createAdmin('test', [
            'entity' => 'test',
            'form' => 'test',
        ]));
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

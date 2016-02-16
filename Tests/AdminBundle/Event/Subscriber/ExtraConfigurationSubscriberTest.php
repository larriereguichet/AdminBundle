<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Event\Subscriber;

use LAG\AdminBundle\Admin\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Event\AdminEvent;
use LAG\AdminBundle\Event\Subscriber\ExtraConfigurationSubscriber;
use LAG\AdminBundle\Tests\Base;

class ExtraConfigurationSubscriberTest extends Base
{
    /**
     * ExtraConfigurationSubscriber SHOULD subscribe to the Admin creation and the Action creation event
     */
    public function testGetSubscribedEvents()
    {
        $subscribedEvents = ExtraConfigurationSubscriber::getSubscribedEvents();

        $this->assertArrayHasKey(AdminEvent::ADMIN_CREATE, $subscribedEvents);
        $this->assertArrayHasKey(AdminEvent::ACTION_CREATE, $subscribedEvents);
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
            $this->mockEntityManager(),
            new ApplicationConfiguration([], 'fr')
        );
        $event = new AdminEvent();
        $event->setConfiguration([]);
        $subscriber->adminCreate($event);
        $this->assertEquals([], $event->getConfiguration());

        // with extra configuration enabled, adminCreate method SHOULD fill action configuration if it is empty
        $subscriber = new ExtraConfigurationSubscriber(
            true,
            $this->mockEntityManager(),
            new ApplicationConfiguration([], 'fr')
        );
        $event = new AdminEvent();
        $event->setConfiguration([]);
        $subscriber->adminCreate($event);
        $this->assertEquals([
            'actions' => [
                'create' => [],
                'list' => [],
                'edit' => [],
                'delete' => [],
                'batch' => [],
            ]
        ], $event->getConfiguration());

        // adminCreate method SHOULD not modified actual configuration
        $event = new AdminEvent();
        $event->setConfiguration([
            'actions' => [
                'myAction' => []
            ]
        ]);
        $subscriber->adminCreate($event);
        $this->assertEquals([
            'actions' => [
                'myAction' => []
            ]
        ], $event->getConfiguration());

        // adminCreate method SHOULD add batch for list actions if not defined
        $event = new AdminEvent();
        $event->setConfiguration([
            'actions' => [
                'list' => []
            ]
        ]);
        $subscriber->adminCreate($event);
        $this->assertEquals([
            'actions' => [
                'list' => [],
                'batch' => [],
            ]
        ], $event->getConfiguration());

        $event = new AdminEvent();
        $event->setConfiguration([
            'actions' => [
                'list' => [
                    'batch' => false
                ]
            ]
        ]);
        $subscriber->adminCreate($event);
        $this->assertEquals([
            'actions' => [
                'list' => [
                    'batch' => false
                ]
            ]
        ], $event->getConfiguration());
    }
}

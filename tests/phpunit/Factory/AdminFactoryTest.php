<?php

namespace LAG\AdminBundle\Tests\Factory;

use LAG\AdminBundle\Admin\Configuration\AdminConfiguration;
use LAG\AdminBundle\Admin\Factory\AdminConfigurationFactoryInterface;
use LAG\AdminBundle\Admin\Resource\AdminResource;
use LAG\AdminBundle\Admin\Resource\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Event\AdminEvents;
use LAG\AdminBundle\Event\Events\AdminEvent;
use LAG\AdminBundle\Tests\Fixtures\FakeEntity;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class AdminFactoryTest extends TestCase
{
    private MockObject $registry;
    private MockObject $eventDispatcher;
    private MockObject $adminConfigurationFactory;
    private AdminConfiguration $adminConfiguration;
    private \LAG\AdminBundle\Admin\Factory\AdminFactory $adminFactory;

    public function testCreate(): void
    {
        $resource = new AdminResource('my_admin', [
            'entity' => FakeEntity::class,
        ]);

        $this->registry
            ->expects($this->once())
            ->method('get')
            ->with('my_admin')
            ->willReturn($resource)
        ;

        $this->adminConfigurationFactory
            ->expects($this->once())
            ->method('create')
            ->with('my_admin', ['entity' => FakeEntity::class])
            ->willReturn($this->adminConfiguration)
        ;

        $this
            ->eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->willReturnCallback(function ($event, $eventName) {
                $this->assertInstanceOf(AdminEvent::class, $event);
                $this->assertEquals(AdminEvents::ADMIN_CREATE, $eventName);

                return $event;
            })
        ;

        $admin = $this->adminFactory->create('my_admin');
        $this->assertEquals($this->adminConfiguration, $admin->getConfiguration());
    }

    protected function setUp(): void
    {
        $this->registry = $this->createMock(ResourceRegistryInterface::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->adminConfigurationFactory = $this->createMock(AdminConfigurationFactoryInterface::class);
        $this->adminConfiguration = new AdminConfiguration();
        $this->adminConfiguration->configure([
            'name' => 'my_admin',
            'entity' => FakeEntity::class,
        ]);

        $this->adminFactory = new \LAG\AdminBundle\Admin\Factory\AdminFactory(
            $this->registry,
            $this->eventDispatcher,
            $this->adminConfigurationFactory
        );
    }
}

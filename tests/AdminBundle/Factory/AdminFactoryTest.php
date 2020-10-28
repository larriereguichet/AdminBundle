<?php

namespace LAG\AdminBundle\Tests\Factory;

use LAG\AdminBundle\Admin\Resource\AdminResource;
use LAG\AdminBundle\Admin\Resource\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Configuration\AdminConfiguration;
use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Event\AdminEvents;
use LAG\AdminBundle\Event\Events\AdminEvent;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Factory\AdminFactory;
use LAG\AdminBundle\Factory\Configuration\AdminConfigurationFactoryInterface;
use LAG\AdminBundle\Tests\Fixtures\FakeEntity;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class AdminFactoryTest extends TestCase
{
    private MockObject $registry;
    private MockObject $eventDispatcher;
    private MockObject $adminConfigurationFactory;
    private AdminConfiguration $adminConfiguration;
    private ApplicationConfiguration $appConfig;
    private AdminFactory $adminFactory;

    public function testCreateAdminFromRequest(): void
    {
        $request = new Request([], [], [
                '_route_params' => [
                '_admin' => 'my_admin',
                '_action' => 'my_action',
            ],
        ]);
        $resource = new AdminResource('my_admin', [
            'entity' => FakeEntity::class,
        ]);

        $this->registry
            ->expects($this->once())
            ->method('has')
            ->with('my_admin')
            ->willReturn(true)
        ;
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

        $this->adminFactory->createFromRequest($request);
    }

    public function testCreateWithoutSupports(): void
    {
        $this->expectException(Exception::class);
        $this->adminFactory->createFromRequest(new Request());
    }

    public function testSupports(): void
    {
        $supports = $this->adminFactory->supports(new Request());
        $this->assertFalse($supports);

        $supports = $this->adminFactory->supports(new Request(['_route_params' => []]));
        $this->assertFalse($supports);

        $supports = $this->adminFactory->supports(new Request(['_route_params' => [
            '_admin' => 'empty_admin',
            '_action' => 'empty_action',
        ]]));
        $this->assertFalse($supports);
    }

    protected function setUp(): void
    {
        $this->registry = $this->createMock(ResourceRegistryInterface::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->adminConfigurationFactory = $this->createMock(AdminConfigurationFactoryInterface::class);
        $this->appConfig = new ApplicationConfiguration([
            'resources_path' => 'my_directory/',
        ]);
        $this->adminConfiguration = new AdminConfiguration();
        $this->adminConfiguration->configure([
            'name' => 'my_admin',
            'entity' => FakeEntity::class,
        ]);

        $this->adminFactory = new AdminFactory(
            $this->registry,
            $this->eventDispatcher,
            $this->adminConfigurationFactory,
            $this->appConfig
        );
    }
}

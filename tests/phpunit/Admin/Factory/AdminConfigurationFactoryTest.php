<?php

namespace LAG\AdminBundle\Tests\Admin\Factory;

use LAG\AdminBundle\Admin\Factory\AdminConfigurationFactory;
use LAG\AdminBundle\Event\Events\Configuration\AdminConfigurationEvent;
use LAG\AdminBundle\Exception\ConfigurationException;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class AdminConfigurationFactoryTest extends TestCase
{
    private AdminConfigurationFactory $factory;
    private MockObject $eventDispatcher;

    /** @dataProvider createDataProvider */
    public function testCreate(array $options, array $expectedConfiguration): void
    {
        $this
            ->eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with(new AdminConfigurationEvent('an_admin_name', $options))
        ;
        $result = $this->factory->create('an_admin_name', $options);

        $this->assertTrue($result->isFrozen());
        $this->assertEquals($expectedConfiguration, $result->toArray());
    }

    protected function createDataProvider(): array
    {
        $data = [];

        $config1 = [
            'entity' => 'AnEntityClass',
            'name' => 'an_admin_name',
        ];
        $expected1 = $this->getAdminDefaultConfiguration();
        $data[] = [$config1, $expected1];

        return $data;
    }

    public function testCreateWithException(): void
    {
        $this->expectException(ConfigurationException::class);
        $this->factory->create('', []);
    }

    protected function setUp(): void
    {
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->factory = new AdminConfigurationFactory($this->eventDispatcher);
    }
}

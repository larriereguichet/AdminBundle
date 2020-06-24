<?php

namespace LAG\AdminBundle\Tests\Event\Subscriber;

use LAG\AdminBundle\Admin\Helper\AdminHelperInterface;
use LAG\AdminBundle\Event\Menu\MenuConfigurationEvent;
use LAG\AdminBundle\Event\Subscriber\MenuSubscriber;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Resource\AdminResource;
use LAG\AdminBundle\Resource\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Tests\AdminTestBase;
use PHPUnit\Framework\MockObject\MockObject;

class MenuSubscriberTest extends AdminTestBase
{
    public function testServiceExists(): void
    {
        $this->assertServiceExists(MenuSubscriber::class);
    }

    public function testSubscribedEvents(): void
    {
        list($subscriber) = $this->createSubscriber(true, []);

        $this->assertSubscribedMethodsExists($subscriber);
    }

    /**
     * @dataProvider supportProvider
     */
    public function testSupports($enabled, $menuName, $configuration, $expected, $exception = null): void
    {
        list($subscriber) = $this->createSubscriber($enabled, $configuration);
        $event = new MenuConfigurationEvent($menuName);

        if ($exception) {
            $this->expectException($exception);
        }
        $subscriber->defineMenuConfiguration($event);
        $this->assertEquals($expected, $event->getMenuConfiguration());
    }

    public function supportProvider(): array
    {
        return [
            [false, 'left', [], []],
            [true, 'left', ['top' => []], []],
            [true, 'left', ['left' => false], []],
            [true, 'left', ['left' => []], [
                'children' => [],
            ]],
            [true, 'left', ['left' => [
                'children' => [
                    'test' => [],
                ],
            ]], [
                'children' => [
                    'test' => [],
                ],
            ], Exception::class],

        ];
    }

    public function testLeftMenu(): void
    {
        list($subscriber, $registry) = $this->createSubscriber(true, [
            'left' => [],
        ]);
        $event = new MenuConfigurationEvent('left');
        $resource = $this->createMock(AdminResource::class);
        $resource
            ->expects($this->atLeastOnce())
            ->method('getConfiguration')
            ->willReturn([
                'actions' => ['list' => []],
            ])
        ;

        $wrongResource = $this->createMock(AdminResource::class);

        $registry
            ->expects($this->once())
            ->method('all')
            ->willReturn(['panda' => $resource, 'bamboo' => $wrongResource])
        ;

        $subscriber->defineMenuConfiguration($event);
        $this->assertEquals([
            'children' => [
                'panda' => [
                    'admin' => 'panda',
                    'action' => 'list',
                ],
            ]
        ], $event->getMenuConfiguration());
    }

    /**
     * @param array $adminMenuConfigurations
     *
     * @return MockObject[]|MenuSubscriber[]
     */
    private function createSubscriber(bool $menuEnabled, array $menuConfigurations)
    {
        $registry = $this->createMock(ResourceRegistryInterface::class);
        $helper = $this->createMock(AdminHelperInterface::class);

        $subscriber = new MenuSubscriber(
            $menuEnabled,
            $registry,
            $helper,
            $menuConfigurations
        );

        return [
            $subscriber,
            $registry,
        ];
    }
}

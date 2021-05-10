<?php

namespace LAG\AdminBundle\Tests\Event\Listener\Menu;

use LAG\AdminBundle\Admin\Helper\AdminHelperInterface;
use LAG\AdminBundle\Admin\Resource\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Event\Events\Configuration\MenuConfigurationEvent;
use LAG\AdminBundle\Event\Listener\Menu\DefaultMenuListener;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class DefaultMenuListenerTest extends TestCase
{
    public function testInvokeWithMenuDisabled(): void
    {
        [$listener] = $this->createListener([]);

        $event = $this->createMock(MenuConfigurationEvent::class);
        $event
            ->expects($this->once())
            ->method('getMenuName')
            ->willReturn('my_menu')
        ;

        $listener->__invoke($event);

        $this->assertEquals([], $event->getMenuConfiguration());
    }

    public function testInvokeWithMissingMenu(): void
    {
        [$listener] = $this->createListener([]);

        $event = $this->createMock(MenuConfigurationEvent::class);
        $event
            ->expects($this->once())
            ->method('getMenuName')
            ->willReturn('missing')
        ;

        $listener->__invoke($event);
    }

    public function testInvokeWithFalseMenu(): void
    {
        [$listener] = $this->createListener([
            'left' => false,
        ]);

        $event = $this->createMock(MenuConfigurationEvent::class);
        $event
            ->expects($this->exactly(2))
            ->method('getMenuName')
            ->willReturn('left')
        ;

        $listener->__invoke($event);
    }

    public function testInvokeWithNullMenu(): void
    {
        [$listener] = $this->createListener([
            'left' => null,
        ]);

        $event = $this->createMock(MenuConfigurationEvent::class);
        $event
            ->expects($this->atLeastOnce())
            ->method('getMenuName')
            ->willReturn('left')
        ;

        $listener->__invoke($event);
    }

    public function testInvokeLeftMenuConfigured(): void
    {
        [$listener] = $this->createListener([
            'left' => [
                'children' => [
                    'my_item' => [
                        'admin' => 'my_admin',
                        'action' => 'my_action',
                    ],
                ],
            ],
        ]);

        $event = $this->createMock(MenuConfigurationEvent::class);
        $event
            ->expects($this->atLeastOnce())
            ->method('getMenuName')
            ->willReturn('left')
        ;
        $event
            ->expects($this->once())
            ->method('setMenuConfiguration')
            ->with([
                'children' => [
                    'my_item' => [
                        'admin' => 'my_admin',
                        'action' => 'my_action',
                        'attributes' => [
                            'class' => 'nav-item',
                        ],
                        'linkAttributes' => [
                            'class' => 'nav-link',
                        ],
                    ],
                ],
                'attributes' => [
                    'id' => 'accordionSidebar',
                    'class' => 'navbar-nav bg-gradient-primary sidebar sidebar-dark accordion',
                ],
                'extras' => [
                    'brand' => true,
                    'homepage' => true,
                ],
            ])
        ;

        $listener->__invoke($event);
    }

    /**
     * @return DefaultMenuListener[]|MockObject[]
     */
    private function createListener(array $menuConfigurations): array
    {
        $registry = $this->createMock(ResourceRegistryInterface::class);
        $adminHelper = $this->createMock(AdminHelperInterface::class);

        $listener = new DefaultMenuListener($registry, $adminHelper, $menuConfigurations);

        return [$listener, $registry, $adminHelper];
    }
}

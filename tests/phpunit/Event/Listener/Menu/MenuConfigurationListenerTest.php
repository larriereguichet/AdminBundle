<?php

namespace LAG\AdminBundle\Tests\Event\Listener\Menu;

use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Admin\Helper\AdminHelperInterface;
use LAG\AdminBundle\Event\Events\Configuration\MenuConfigurationEvent;
use LAG\AdminBundle\Event\Listener\Menu\MenuConfigurationListener;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class MenuConfigurationListenerTest extends TestCase
{
    private MockObject $adminHelper;
    private MenuConfigurationListener $listener;

    public function testInvoke(): void
    {
        $event = $this->createMock(MenuConfigurationEvent::class);
        $admin = $this->createMock(AdminInterface::class);

        $this
            ->adminHelper
            ->expects($this->once())
            ->method('hasAdmin')
            ->willReturn(true)
        ;

        $this
            ->adminHelper
            ->expects($this->once())
            ->method('getAdmin')
            ->willReturn($admin)
        ;

        $this->listener->__invoke($event);
    }

    public function testInvokeWithoutAdmin(): void
    {
        $event = $this->createMock(MenuConfigurationEvent::class);

        $this
            ->adminHelper
            ->expects($this->once())
            ->method('hasAdmin')
            ->willReturn(false)
        ;

        $this
            ->adminHelper
            ->expects($this->never())
            ->method('getAdmin')
        ;

        $this->listener->__invoke($event);
    }

    protected function setUp(): void
    {
        $this->adminHelper = $this->createMock(AdminHelperInterface::class);
        $this->listener = new MenuConfigurationListener([], $this->adminHelper);
    }
}

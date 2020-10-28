<?php

namespace AdminBundle\Twig\Extension;

use LAG\AdminBundle\Menu\Provider\MenuProvider;
use LAG\AdminBundle\Tests\TestCase;
use LAG\AdminBundle\Twig\Extension\MenuExtension;
use PHPUnit\Framework\MockObject\MockObject;

class MenuExtensionTest extends TestCase
{
    private MenuExtension $extension;
    private MockObject $menuProvider;

    public function testServiceExists(): void
    {
        $this->assertServiceExists(MenuExtension::class);
    }

    public function testGetFunctions(): void
    {
        $functions = $this->extension->getFunctions();

        $this->assertCount(1, $functions);
        $this->assertEquals('admin_has_menu', $functions[0]->getName());
    }

    public function testHasMenu(): void
    {
        $this
            ->menuProvider
            ->expects($this->once())
            ->method('has')
            ->willReturn(true)
        ;

        $this->assertTrue($this->extension->hasMenu('my_menu', []));
    }

    protected function setUp(): void
    {
        $this->menuProvider = $this->createMock(MenuProvider::class);
        $this->extension = new MenuExtension($this->menuProvider);
    }
}

<?php

namespace LAG\AdminBundle\Tests\Menu\Provider;

use Knp\Menu\ItemInterface;
use LAG\AdminBundle\Factory\Configuration\ConfigurationFactoryInterface;
use LAG\AdminBundle\Menu\Factory\MenuFactoryInterface;
use LAG\AdminBundle\Menu\Provider\MenuProvider;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Security\Core\Security;

class MenuProviderTest extends TestCase
{
    private MenuProvider $provider;
    private MockObject $menuFactory;
    private MockObject $configurationFactory;
    private MockObject $security;

    public function testGet(): void
    {
        $menu = $this->createMock(ItemInterface::class);
        $this
            ->menuFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($menu)
        ;

        $result = $this->provider->get('my_menu', [
            'my_options' => 'options',
        ]);
        $this->assertEquals($menu, $result);
    }

    protected function setUp(): void
    {
        $this->menuFactory = $this->createMock(MenuFactoryInterface::class);
        $this->configurationFactory = $this->createMock(ConfigurationFactoryInterface::class);
        $this->security = $this->createMock(Security::class);
        $this->provider = new MenuProvider(
            [],
            $this->menuFactory,
            $this->configurationFactory,
            $this->security
        );
    }
}

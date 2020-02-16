<?php

namespace LAG\AdminBundle\Tests\Factory;

use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Configuration\ApplicationConfigurationStorage;
use LAG\AdminBundle\Factory\MenuFactory;
use LAG\AdminBundle\Menu\Menu;
use LAG\AdminBundle\Tests\AdminTestBase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MenuFactoryTest extends AdminTestBase
{
    public function testServiceExists()
    {
        $this->assertServiceExists(MenuFactory::class);
    }

    public function testCreate()
    {
        list($factory,, $requestStack,) = $this->createFactory();

        $requestStack
            ->expects($this->atLeastOnce())
            ->method('getCurrentRequest')
            ->willReturn(new Request([], [], [
                '_route' => 'planet_explode',
            ]));

        $menu = $factory->create('main', [
            'items' => [
                'death_star_explode' => [
                    'admin' => 'planets',
                    'action' => 'explode',
                ],
            ],
        ]);

        $this->assertInstanceOf(Menu::class, $menu);
        $this->assertEquals('main', $menu->getName());
        $this->assertEquals(null, $menu->get('position'));

        // Test an admin item
        $item = $menu->getItems()[0];
        $this->assertEquals('planets', $item->get('admin'));
        $this->assertEquals($item->get('admin'), $item->getConfiguration()->get('admin'));
        $this->assertEquals('explode', $item->get('action'));
        $this->assertEquals(null, $item->get('route'));
        $this->assertEquals(null, $item->get('url'));
        $this->assertEquals([], $item->get('parameters'));
        $this->assertEquals('Planets', $item->get('text'));
        $this->assertArrayHasKey('id', $item->get('attr'));
        $this->assertEquals([], $item->get('items'));
        $this->assertEquals(null, $item->get('icon'));
        $this->assertEquals('nav-link', $item->get('link_css_class'));


        $this->assertEquals([], $menu->get('attr'));
        $this->assertEquals('list-group nav flex-column navbar-nav menu-main', $menu->get('css_class'));
        $this->assertEquals('', $menu->get('item_css_class'));
        $this->assertEquals('nav-link', $menu->get('link_css_class'));
        $this->assertEquals('', $menu->get('template'));
        $this->assertEquals(null, $menu->get('brand'));
    }

    public function testCreateWithEmptyConfiguration()
    {
        list($factory) = $this->createFactory();

        $menu = $factory->create('main', []);

        $this->assertInstanceOf(Menu::class, $menu);
        $this->assertEquals('main', $menu->getName());
        $this->assertEquals(null, $menu->get('position'));
        $this->assertEquals([], $menu->get('items'));
        $this->assertEquals([], $menu->get('attr'));
        $this->assertEquals('list-group nav flex-column navbar-nav menu-main', $menu->get('css_class'));
        $this->assertEquals('', $menu->get('item_css_class'));
        $this->assertEquals('nav-link', $menu->get('link_css_class'));
        $this->assertEquals('', $menu->get('template'));
        $this->assertEquals(null, $menu->get('brand'));
    }

    /**
     * @return MenuFactory[]|MockObject[]
     */
    private function createFactory()
    {
        $resolver = new OptionsResolver();
        $applicationConfiguration = new ApplicationConfiguration();
        $applicationConfiguration->configureOptions($resolver);
        $applicationConfiguration->setParameters($resolver->resolve([]));

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $storage = $this->createMock(ApplicationConfigurationStorage::class);
        $storage
            ->expects($this->atLeastOnce())
            ->method('getConfiguration')
            ->willReturn($applicationConfiguration);

        $requestStack = $this->createMock(RequestStack::class);

        $factory = new MenuFactory($requestStack, $storage, $eventDispatcher);

        return [
            $factory,
            $applicationConfiguration,
            $requestStack,
            $storage,
        ];
    }
}

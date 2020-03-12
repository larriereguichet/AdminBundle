<?php

namespace LAG\AdminBundle\Tests\Menu\Provider;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use LAG\AdminBundle\Configuration\MenuConfiguration;
use LAG\AdminBundle\Factory\ConfigurationFactory;
use LAG\AdminBundle\Menu\Provider\MenuProvider;
use LAG\AdminBundle\Routing\Resolver\RoutingResolverInterface;
use LAG\AdminBundle\Tests\AdminTestBase;
use PHPUnit\Framework\MockObject\MockObject;

class MenuProviderTest extends AdminTestBase
{
    public function testGet()
    {
        list($provider, $menuFactory, $configurationFactory, $resolver) = $this->createProvider();

        $menuConfiguration = $this->createMock(MenuConfiguration::class);
        $menuConfiguration
            ->expects($this->once())
            ->method('all')
            ->willReturn([
                'attributes' => [
                    'class' => 'abitbol',
                ],
                'children' => [
                    'first' => [
                        'text' => 'Yes !',
                        'attributes' => [
                            'class' => 'child-class',
                        ],
                        'linkAttributes' => [
                            'class' => 'link-class',
                        ],
                    ],
                ]
            ])
        ;

        $menuRoot = $this->createMock(ItemInterface::class);
        $menuRoot
            ->expects($this->atLeastOnce())
            ->method('addChild')
            ->with('Yes !', [
                'attributes' => [
                    'class' => 'child-class',
                ],
                'linkAttributes' => [
                    'class' => 'link-class',
                ],
                'route' => 'my_route',
            ])
        ;

        $menuFactory
            ->expects($this->once())
            ->method('createItem')
            ->with('root', [
                'attributes' => [
                    'class' => 'abitbol',
                ],
            ])
            ->willReturn($menuRoot)
        ;

        $configurationFactory
            ->expects($this->once())
            ->method('createMenuConfiguration')
            ->with('my_menu')
            ->willReturn($menuConfiguration)
        ;

        $resolver
            ->expects($this->once())
            ->method('resolveOptions')
            ->with([
                'text' => 'Yes !',
                'attributes' => [
                    'class' => 'child-class',
                ],
                'linkAttributes' => [
                    'class' => 'link-class',
                ],
            ])
            ->willReturn('my_route')
        ;

        $menu = $provider->get('my_menu', []);
        $this->assertEquals($menuRoot, $menu);
    }

    /**
     * @param array $menuConfigurations
     *
     * @return MenuProvider[]|MockObject[]
     */
    private function createProvider(array $menuConfigurations = []): array
    {
        $menuFactory = $this->createMock(FactoryInterface::class);
        $configurationFactory = $this->createMock(ConfigurationFactory::class);
        $resolver = $this->createMock(RoutingResolverInterface::class);

        $provider = new MenuProvider($menuConfigurations, $menuFactory, $configurationFactory, $resolver);

        return [
            $provider,
            $menuFactory,
            $configurationFactory,
            $resolver
        ];
    }
}

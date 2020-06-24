<?php

namespace LAG\AdminBundle\Tests\Menu\Provider;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use LAG\AdminBundle\Bridge\KnpMenu\Provider\MenuProvider;
use LAG\AdminBundle\Configuration\MenuConfiguration;
use LAG\AdminBundle\Factory\ConfigurationFactory;
use LAG\AdminBundle\Tests\AdminTestBase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class MenuProviderTest extends AdminTestBase
{
    public function testGet()
    {
        list($provider, $menuFactory, $configurationFactory, $requestStack) = $this->createProvider();

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
                        'route' => 'road 66',
                        'icon' => 'fa-yes',
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
                'route' => 'road 66',
                'text' => 'Yes !',
                'icon' => 'fa-yes',
            ])
        ;

        $requestStack
            ->expects($this->once())
            ->method('getMasterRequest')
            ->willReturn(new Request([], [], ['_route' =>'road 66']))
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

        $menu = $provider->get('my_menu', []);
        $this->assertEquals($menuRoot, $menu);
    }

    public function testHas(): void
    {
        [$provider] = $this->createProvider([
            'left' => []
        ]);

        $this->assertTrue($provider->has('left'));
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
        $requestStack = $this->createMock(RequestStack::class);

        $provider = new MenuProvider(
            $menuConfigurations,
            $menuFactory,
            $configurationFactory,
            $requestStack
        );

        return [
            $provider,
            $menuFactory,
            $configurationFactory,
            $requestStack
        ];
    }
}

<?php

namespace LAG\AdminBundle\Tests\Menu\Provider;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use LAG\AdminBundle\Admin\Helper\AdminHelperInterface;
use LAG\AdminBundle\Configuration\MenuConfiguration;
use LAG\AdminBundle\Factory\Configuration\ConfigurationFactoryInterface;
use LAG\AdminBundle\Menu\Provider\MenuProvider;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;

class MenuProviderTest extends TestCase
{
    public function testGet()
    {
        [$provider, $menuFactory, $configurationFactory, $requestStack] = $this->createProvider();

        $menuConfiguration = $this->createMock(MenuConfiguration::class);
        $menuConfiguration
            ->expects($this->once())
            ->method('toArray')
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
                ],
                'extras' => [],
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
            ->willReturn(new Request([], [], ['_route' => 'road 66']))
        ;

        $menuFactory
            ->expects($this->once())
            ->method('createItem')
            ->with('root', [
                'attributes' => [
                    'class' => 'abitbol',
                ],
                'extras' => [],
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
            'left' => [],
        ]);

        $this->assertTrue($provider->has('left'));
    }

    /**
     * @return MenuProvider[]|MockObject[]
     */
    private function createProvider(array $menuConfigurations = []): array
    {
        $menuFactory = $this->createMock(FactoryInterface::class);
        $configurationFactory = $this->createMock(ConfigurationFactoryInterface::class);
        $requestStack = $this->createMock(RequestStack::class);
        $security = $this->createMock(Security::class);
        $adminHelper = $this->createMock(AdminHelperInterface::class);

        $provider = new MenuProvider(
            $menuConfigurations,
            $menuFactory,
            $configurationFactory,
            $requestStack,
            $security,
            $adminHelper
        );

        return [
            $provider,
            $menuFactory,
            $configurationFactory,
            $requestStack,
            $security,
            $adminHelper,
        ];
    }
}

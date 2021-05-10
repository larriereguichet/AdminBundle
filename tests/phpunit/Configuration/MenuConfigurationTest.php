<?php

namespace LAG\AdminBundle\Tests\Configuration;

use JK\Configuration\Exception\InvalidConfigurationException;
use LAG\AdminBundle\Configuration\MenuConfiguration;
use LAG\AdminBundle\Tests\TestCase;

class MenuConfigurationTest extends TestCase
{
    /**
     * @dataProvider configurationDataProvider
     */
    public function testConfiguration(string $menuName, array $options, array $expectedOptions): void
    {
        if (count($expectedOptions) === 0) {
            $this->expectException(InvalidConfigurationException::class);
        }
        $configuration = new MenuConfiguration($menuName);
        $configuration->configure($options);

        $this->assertEquals($expectedOptions, $configuration->toArray());
        $this->assertEquals($menuName, $configuration->getMenuName());
        $this->assertEquals($expectedOptions['extras'], $configuration->getExtras());

        foreach ($expectedOptions['extras'] as $name => $value) {
            $this->assertTrue($configuration->hasExtra($name));
            $this->assertEquals($value, $configuration->getExtra($name));
        }

        if (key_exists('permissions', $expectedOptions['extras'])) {
            $this->assertTrue($configuration->hasPermissions());
        }
        $this->assertEquals($expectedOptions['extras']['permissions'], $configuration->getPermissions());
    }

    public function configurationDataProvider(): array
    {
        return [
            [
                'my_menu',
                [
                    'children' => null,
                    'extras' => null,
                ],
                [
                    'attributes' => [],
                    'children' => [],
                    'extras' => [
                        'permissions' => ['ROLE_USER'],
                    ],
                    'inherits' => true,
                ],
            ],
            [
                'my_menu_with_children',
                [
                    'children' => [
                        'test' => [
                            'admin' => 'my_admin',
                            'action' => 'my_action',
                        ],
                    ],
                ],
                [
                    'attributes' => [],
                    'children' => [
                        'test' => [
                            'route' => null,
                            'routeParameters' => [],
                            'uri' => null,
                            'attributes' => [],
                            'label' => null,
                            'icon' => null,
                            'extras' => [
                                'safe_label' => true,
                            ],
                            'admin' => 'my_admin',
                            'action' => 'my_action',
                            'linkAttributes' => [],
                            'text' => 'My Admin',
                            'children' => [],
                        ],
                    ],
                    'extras' => [
                        'permissions' => ['ROLE_USER'],
                    ],
                    'inherits' => true,
                ],
            ],
            [
                'my_wrong_menu',
                [
                    'children' => [
                        'test' => null,
                    ],
                ],
                [],
            ],
        ];
    }
}

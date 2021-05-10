<?php

namespace LAG\AdminBundle\Tests\Configuration;

use JK\Configuration\Exception\InvalidConfigurationException;
use LAG\AdminBundle\Configuration\MenuItemConfiguration;
use LAG\AdminBundle\Tests\TestCase;

class MenuItemConfigurationTest extends TestCase
{
    /**
     * @dataProvider configurationDataProvider
     */
    public function testConfiguration(string $itemName, string $menuName, array $options, array $expectedOptions): void
    {
        if ($itemName === 'error_item') {
            $this->expectException(InvalidConfigurationException::class);
        }
        $configuration = new MenuItemConfiguration($itemName, $menuName);
        $configuration->configure($options);

        $this->assertEquals($expectedOptions, $configuration->toArray());
    }

    public function configurationDataProvider(): array
    {
        return [
            [
                'my_item',
                'left',
                [
                    'admin' => 'my_admin',
                    'action' => 'my_action',
                ],
                [
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
                    'linkAttributes' => [
                        'class' => 'list-group-item list-group-item-action',
                    ],
                    'text' => 'My Admin',
                    'children' => [],
                ],
            ],
            [
                'my_list_item',
                'left',
                [
                    'admin' => 'my_admin',
                ],
                [
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
                    'action' => 'list',
                    'linkAttributes' => [
                        'class' => 'list-group-item list-group-item-action',
                    ],
                    'text' => 'My Admins',
                    'children' => [],
                ],
            ],
            [
                'my_item',
                'left',
                [
                    'admin' => 'my_admin',
                    'action' => 'list',
                ],
                [
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
                    'action' => 'list',
                    'linkAttributes' => [
                        'class' => 'list-group-item list-group-item-action',
                    ],
                    'text' => 'My Admins',
                    'children' => [],
                ],
            ],
            [
                'my_item',
                'left',
                [
                    'admin' => 'category',
                    'action' => 'list',
                ],
                [
                    'route' => null,
                    'routeParameters' => [],
                    'uri' => null,
                    'attributes' => [],
                    'label' => null,
                    'icon' => null,
                    'extras' => [
                        'safe_label' => true,
                    ],
                    'admin' => 'category',
                    'action' => 'list',
                    'linkAttributes' => [
                        'class' => 'list-group-item list-group-item-action',
                    ],
                    'text' => 'Categories',
                    'children' => [],
                ],
            ],
            [
                'my_item',
                'left',
                [
                    'route' => 'my_route',
                ],
                [
                    'route' => 'my_route',
                    'routeParameters' => [],
                    'uri' => null,
                    'attributes' => [],
                    'label' => null,
                    'icon' => null,
                    'extras' => [
                        'safe_label' => true,
                    ],
                    'admin' => null,
                    'action' => null,
                    'linkAttributes' => [
                        'class' => 'list-group-item list-group-item-action',
                    ],
                    'text' => 'My Item',
                    'children' => [],
                ],
            ],
            [
                'my_text_item',
                'left',
                [
                    'route' => 'my_route',
                    'text' => 'MyText',
                    'children' => null,
                ],
                [
                    'route' => 'my_route',
                    'routeParameters' => [],
                    'uri' => null,
                    'attributes' => [],
                    'label' => null,
                    'icon' => null,
                    'extras' => [
                        'safe_label' => true,
                    ],
                    'admin' => null,
                    'action' => null,
                    'linkAttributes' => [
                        'class' => 'list-group-item list-group-item-action',
                    ],
                    'text' => 'MyText',
                    'children' => [],
                ],
            ],
            [
                'my_children_item',
                'left',
                [
                    'route' => 'my_route',
                    'children' => [
                        'my_child' => ['route' => 'a_route'],
                    ],
                ],
                [
                    'route' => 'my_route',
                    'routeParameters' => [],
                    'uri' => null,
                    'attributes' => [],
                    'label' => null,
                    'icon' => null,
                    'extras' => [
                        'safe_label' => true,
                    ],
                    'admin' => null,
                    'action' => null,
                    'linkAttributes' => [
                        'class' => 'list-group-item list-group-item-action',
                    ],
                    'text' => 'My Children Item',
                    'children' => [
                        'my_child' => [
                            'routeParameters' => [],
                            'uri' => null,
                            'attributes' => [],
                            'label' => null,
                            'icon' => null,
                            'extras' => [
                                'safe_label' => true,
                            ],
                            'route' => 'a_route',
                            'admin' => null,
                            'action' => null,
                            'linkAttributes' => [
                                'class' => 'list-group-item list-group-item-action',
                            ],
                            'text' => 'My Child',
                            'children' => [],
                        ],
                    ],
                ],
            ],
            [
                'error_item',
                'error',
                [],
                [],
            ],
            [
                'error_item',
                'error',
                [
                    'action' => 'no_admin',
                    'uri' => 'test',
                ],
                [],
            ],
        ];
    }
}

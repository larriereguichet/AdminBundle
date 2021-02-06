<?php

namespace LAG\AdminBundle\Tests\Admin\Configuration;

use LAG\AdminBundle\Admin\Configuration\ActionConfigurationMapper;
use LAG\AdminBundle\Configuration\AdminConfiguration;
use LAG\AdminBundle\Tests\TestCase;

class ActionConfigurationMapperTest extends TestCase
{
    private ActionConfigurationMapper $mapper;

    /**
     * @dataProvider mapProvider
     */
    public function testMap(array $configuration, array $expectedConfiguration): void
    {
        $adminConfiguration = new AdminConfiguration();
        $adminConfiguration->configure($configuration);

        $map = $this->mapper->map('my_action', $adminConfiguration);
        $this->assertEquals($expectedConfiguration, $map);
    }

    public function mapProvider(): array
    {
        return [
            [
                [
                    'entity' => 'my_entity',
                    'name' => 'my_admin',
                ],
                [
                    'class' => 'LAG\AdminBundle\Admin\Action',
                    'routes_pattern' => 'lag_admin.{admin}.{action}',
                    'max_per_page' => 25,
                    'pager' => 'pagerfanta',
                    'permissions' => ['ROLE_ADMIN'],
                    'string_length' => 200,
                    'string_truncate' => '...',
                    'date_format' => 'Y-m-d',
                    'page_parameter' => 'page',
                    'template' => '',
                    'menus' => [],
                ],
            ],
        ];
    }

    protected function setUp(): void
    {
        $this->mapper = new ActionConfigurationMapper();
    }
}

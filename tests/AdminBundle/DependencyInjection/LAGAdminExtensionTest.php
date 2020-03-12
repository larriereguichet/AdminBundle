<?php

namespace LAG\AdminBundle\Tests\DependencyInjection;

use LAG\AdminBundle\DependencyInjection\LAGAdminExtension;
use LAG\AdminBundle\Tests\AdminTestBase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class LAGAdminExtensionTest extends AdminTestBase
{
    /**
     * The load should allow the container to compile without errors.
     */
    public function testLoad()
    {
        $builder = $this->createMock(ContainerBuilder::class);
        $builder
            ->expects($this->atLeastOnce())
            ->method('setParameter')
            ->willReturnCallback(function($parameter, $value) {
                $this->assertContains($parameter, [
                    'lag.admin.enable_extra_configuration',
                    'lag.admin.application_configuration',
                    'lag.admins',
                    'lag.menus',
                    'lag.enable_menus',
                    'lag.admin.resources_path',
                ]);

                if ('lag.admin.enable_extra_configuration' === $parameter) {
                    $this->assertContains($value, [
                        true,
                    ]);
                }
            })
        ;

        $extension = new LAGAdminExtension();
        $extension->load([
            'application' => [
                //'enable_extra_configuration' => true,
                'application' => [
                    'enable_menus' => true,
                ],
            ],
        ], $builder);
    }

    /**
     * The load should allow the container to compile without errors.
     */
    public function testLoadWithoutConfiguration()
    {
        $builder = $this->createMock(ContainerBuilder::class);

        $extension = new LAGAdminExtension();
        $extension->load([], $builder);
        // Everything went fine
        $this->assertTrue(true);
    }
}

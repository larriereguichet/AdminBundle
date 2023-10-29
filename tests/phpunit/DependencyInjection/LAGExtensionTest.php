<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\DependencyInjection;

use LAG\AdminBundle\DependencyInjection\LAGAdminExtension;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class LAGExtensionTest extends TestCase
{
    private MockObject $container;

    /**
     * The load should allow the container to compile without errors.
     */
    public function testLoad(): void
    {
        $this
            ->container
            ->expects($this->atLeastOnce())
            ->method('setParameter')
            ->willReturnCallback(function ($parameter, $value) {
                $this->assertContains($parameter, [
                    'lag_admin.application.configuration',
                    'lag_admin.resource_paths',
                    'lag_admin.title',
                    'lag_admin.translation_domain',
                    'lag_admin.resource_paths',
                    'lag_admin.date_format',
                    'lag_admin.time_format',
                    'lag_admin.date_localization',
                    'lag_admin.filter_events',
                    'lag_admin.media_bundle_enabled',
                    'lag_admin.grids',
                ]);
            })
        ;

        $extension = new LAGAdminExtension();
        $extension->load([], $this->container);
    }

    /**
     * The load should allow the container to compile without errors.
     */
    public function testLoadWithoutConfiguration(): void
    {
        $extension = new LAGAdminExtension();
        $extension->load([
            'kernel.bundles' => [],
        ], $this->container);

        $this->assertTrue(true);
    }

    protected function setUp(): void
    {
        $this->container = $this->createMock(ContainerBuilder::class);
        $this->container
            ->expects($this->atLeastOnce())
            ->method('getParameter')
            ->willReturnMap([
                ['kernel.environment', 'dev'],
                ['kernel.bundles', []],
            ])
        ;
    }
}

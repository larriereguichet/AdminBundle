<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\DependencyInjection;

use LAG\AdminBundle\DependencyInjection\LAGAdminExtension;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class LAGExtensionTest extends TestCase
{
    private MockObject $container;

    #[Test]
    public function itLoadConfiguration(): void
    {
        $this
            ->container
            ->expects($this->atLeastOnce())
            ->method('setParameter')
            ->willReturnCallback(function ($parameter): void {
                $this->assertContains($parameter, [
                    'lag_admin.application_parameter',
                    'lag_admin.media_directory',
                    'lag_admin.upload_storage',
                    'lag_admin.resource_parameter',
                    'lag_admin.operation_parameter',
                    'lag_admin.application.configuration',
                    'lag_admin.resource_paths',
                    'lag_admin.title',
                    'lag_admin.resource_paths',
                    'lag_admin.date_format',
                    'lag_admin.time_format',
                    'lag_admin.date_localization',
                    'lag_admin.filter_events',
                    'lag_admin.media_bundle_enabled',
                    'lag_admin.grids',
                    'lag_admin.grid_paths',
                    'lag_admin.grids_templates',
                ]);
            })
        ;

        $extension = new LAGAdminExtension();
        $extension->load([
            'lag_admin' => [
                'mapping' => ['paths' => [__DIR__.'/../../app/src/Entity']],
            ],
        ], $this->container); // @phpstan-ignore-line
    }

    #[Test]
    public function testLoadWithoutConfiguration(): void
    {
        $extension = new LAGAdminExtension();
        $extension->load([
            'kernel.bundles' => [],
            'lag_admin' => [
                'mapping' => ['paths' => [__DIR__.'/../../app/src/Entity']],
            ],
        ], $this->container); // @phpstan-ignore-line
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
                ['kernel.project_dir', __DIR__.'/../../app'],
            ])
        ;
    }
}

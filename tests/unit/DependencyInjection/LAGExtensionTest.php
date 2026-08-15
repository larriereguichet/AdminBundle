<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\DependencyInjection;

use LAG\AdminBundle\DependencyInjection\LAGAdminExtension;
use LAG\AdminBundle\Tests\Unit\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class LAGExtensionTest extends TestCase
{
    private MockObject $container;

    #[Test]
    public function itLoadConfiguration(): void
    {
        $this->container
            ->expects($this->atLeastOnce())
            ->method('setParameter')
            ->willReturnCallback(function ($parameter): void {
                $this->assertContains($parameter, [
                    'lag_admin.mapping_paths',
                    'lag_admin.applications',
                    'lag_admin.media_directory',
                    'lag_admin.media_storage',
                    'lag_admin.request_parameter',
                    'lag_admin.grid_templates',
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

        self::assertSame('lag_admin', $extension->getAlias());
    }

    #[Test]
    public function itReturnsAlias(): void
    {
        $extension = new LAGAdminExtension();
        self::assertSame('lag_admin', $extension->getAlias());
    }

    #[Test]
    public function itPrependsConfiguration(): void
    {
        $this->container->expects($this->atLeastOnce())
            ->method('prependExtensionConfig')
        ;

        $extension = new LAGAdminExtension();
        $extension->prepend($this->container);
    }

    protected function setUp(): void
    {
        $this->container = $this->createMock(ContainerBuilder::class);
        $this->container
            ->method('getParameter')
            ->willReturnMap([
                ['kernel.environment', 'dev'],
                ['kernel.bundles', []],
                ['kernel.project_dir', __DIR__.'/../../app'],
            ])
        ;
    }
}

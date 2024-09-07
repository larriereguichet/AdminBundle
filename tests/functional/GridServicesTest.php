<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Functional;

use LAG\AdminBundle\Grid\Render\GridRenderer;
use LAG\AdminBundle\Grid\Render\GridRendererInterface;
use LAG\AdminBundle\Grid\Render\HeaderRenderer;
use LAG\AdminBundle\Grid\Render\HeaderRendererInterface;
use LAG\AdminBundle\Grid\Render\LinkRenderer;
use LAG\AdminBundle\Grid\Render\LinkRendererInterface;
use LAG\AdminBundle\Grid\ViewBuilder\GridViewBuilder;
use LAG\AdminBundle\Grid\ViewBuilder\GridViewBuilderInterface;
use LAG\AdminBundle\Grid\ViewBuilder\SecurityHeaderViewBuilder;
use LAG\AdminBundle\Tests\ContainerTestTrait;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class GridServicesTest extends TestCase
{
    use ContainerTestTrait;

    #[Test]
    public function servicesExists(): void
    {
        // Renderers
        self::assertService(LinkRendererInterface::class);
        self::assertNoService(LinkRenderer::class);
        self::assertService(GridRendererInterface::class);
        self::assertNoService(GridRenderer::class);
        self::assertService(HeaderRendererInterface::class);
        self::assertNoService(HeaderRenderer::class);

        self::assertService(GridViewBuilderInterface::class);
        self::assertNoService(GridViewBuilder::class);

        self::assertService(SecurityHeaderViewBuilder::class);
    }
}

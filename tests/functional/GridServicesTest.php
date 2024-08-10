<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Functional;

use LAG\AdminBundle\Grid\ViewBuilder\GridViewBuilderInterface;
use LAG\AdminBundle\Tests\ContainerTestTrait;
use LAG\AdminBundle\View\Render\LinkRendererInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class GridServicesTest extends TestCase
{
    use ContainerTestTrait;

    #[Test]
    public function servicesExists(): void
    {
        self::assertService(LinkRendererInterface::class);
        self::assertService(GridViewBuilderInterface::class);
    }
}

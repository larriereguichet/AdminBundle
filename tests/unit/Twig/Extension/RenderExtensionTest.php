<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Twig\Extension;

use LAG\AdminBundle\Tests\Unit\TestCase;
use LAG\AdminBundle\Twig\Extension\RenderExtension;
use LAG\AdminBundle\Twig\Runtime\RenderRuntime;
use PHPUnit\Framework\Attributes\Test;
use Twig\TwigFunction;

final class RenderExtensionTest extends TestCase
{
    #[Test]
    public function itReturnsTwigFunctions(): void
    {
        $extension = new RenderExtension();

        self::assertEquals([
            new TwigFunction('lag_admin_link', [RenderRuntime::class, 'renderLink'], ['is_safe' => ['html']]),
            new TwigFunction('lag_admin_action', [RenderRuntime::class, 'renderAction'], ['is_safe' => ['html']]),
        ], $extension->getFunctions());
    }
}

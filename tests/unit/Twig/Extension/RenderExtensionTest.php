<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Twig\Extension;

use LAG\AdminBundle\Tests\TestCase;
use LAG\AdminBundle\Twig\Extension\RenderExtension;
use LAG\AdminBundle\View\Helper\RenderHelper;
use PHPUnit\Framework\Attributes\Test;
use Twig\TwigFunction;

final class RenderExtensionTest extends TestCase
{
    #[Test]
    public function itReturnsTwigFunctions(): void
    {
        $extension = new RenderExtension();

        self::assertEquals([
            new TwigFunction('lag_admin_link', [RenderHelper::class, 'renderLink'], ['is_safe' => ['html']]),
            new TwigFunction('lag_admin_action', [RenderHelper::class, 'renderAction'], ['is_safe' => ['html']]),
        ], $extension->getFunctions());
    }
}

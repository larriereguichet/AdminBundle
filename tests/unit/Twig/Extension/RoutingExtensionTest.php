<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Twig\Extension;

use LAG\AdminBundle\Tests\Unit\TestCase;
use LAG\AdminBundle\Twig\Extension\RoutingExtension;
use LAG\AdminBundle\Twig\Runtime\RoutingRuntime;
use PHPUnit\Framework\Attributes\Test;
use Twig\TwigFunction;

final class RoutingExtensionTest extends TestCase
{
    #[Test]
    public function itReturnsTwigFunctions(): void
    {
        $extension = new RoutingExtension();

        self::assertEquals([
            new TwigFunction('lag_admin_path', [RoutingRuntime::class, 'generatePath']),
            new TwigFunction('lag_admin_url', [RoutingRuntime::class, 'generateUrl']),
            new TwigFunction('lag_admin_link', [RoutingRuntime::class, 'generateLink']),
        ], $extension->getFunctions());
    }
}

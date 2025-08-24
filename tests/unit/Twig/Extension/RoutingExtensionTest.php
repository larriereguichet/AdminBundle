<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Twig\Extension;

use LAG\AdminBundle\Tests\TestCase;
use LAG\AdminBundle\Twig\Extension\RoutingExtension;
use LAG\AdminBundle\View\Helper\RoutingHelper;
use PHPUnit\Framework\Attributes\Test;
use Twig\TwigFunction;

final class RoutingExtensionTest extends TestCase
{
    #[Test]
    public function itReturnsTwigFunctions(): void
    {
        $extension = new RoutingExtension();

        self::assertEquals([
            new TwigFunction('lag_admin_path', [RoutingHelper::class, 'generatePath']),
            new TwigFunction('lag_admin_url', [RoutingHelper::class, 'generateUrl']),
            new TwigFunction('lag_admin_resource_url', [RoutingHelper::class, 'generateResourceUrl']),
            new TwigFunction('lag_admin_link_url', [RoutingHelper::class, 'generateLinkUrl']),
        ], $extension->getFunctions());
    }
}

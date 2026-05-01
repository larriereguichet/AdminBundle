<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Twig\Extension;

use LAG\AdminBundle\Twig\Runtime\RoutingRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class RoutingExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('lag_admin_path', [RoutingRuntime::class, 'generatePath']),
            new TwigFunction('lag_admin_url', [RoutingRuntime::class, 'generateUrl']),
            new TwigFunction('lag_admin_link', [RoutingRuntime::class, 'generateLink']),
        ];
    }
}

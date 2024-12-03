<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Twig\Extension;

use LAG\AdminBundle\View\Helper\RoutingHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class RoutingExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('lag_admin_path', [RoutingHelper::class, 'generatePath']),
            new TwigFunction('lag_admin_url', [RoutingHelper::class, 'generateUrl']),
            new TwigFunction('lag_admin_resource_url', [RoutingHelper::class, 'generateResourceUrl']),
        ];
    }
}

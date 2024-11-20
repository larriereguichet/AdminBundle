<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Twig\Extension;

use LAG\AdminBundle\View\Helper\RoutingHelperInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class RoutingExtension extends AbstractExtension
{
    public function __construct(
        private readonly RoutingHelperInterface $helper,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('lag_admin_path', $this->helper->generatePath(...)),
            new TwigFunction('lag_admin_url', $this->helper->generateUrl(...)),
            new TwigFunction('lag_admin_resource_url', $this->helper->generateResourceUrl(...)),
        ];
    }
}

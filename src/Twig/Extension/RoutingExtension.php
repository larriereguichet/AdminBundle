<?php

namespace LAG\AdminBundle\Twig\Extension;

use LAG\AdminBundle\View\Helper\RoutingHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class RoutingExtension extends AbstractExtension
{
    public function __construct(
        private readonly RoutingHelper $helper,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('lag_admin_path', [$this->helper, 'generateOperationPath'])
        ];
    }
}

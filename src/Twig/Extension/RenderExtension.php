<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Twig\Extension;

use LAG\AdminBundle\View\Helper\RenderHelperInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class RenderExtension extends AbstractExtension
{
    public function __construct(
        private readonly RenderHelperInterface $helper,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('lag_admin_action', $this->helper->renderAction(...), ['is_safe' => ['html']]),
        ];
    }
}

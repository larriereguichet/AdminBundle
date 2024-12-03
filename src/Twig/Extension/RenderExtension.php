<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Twig\Extension;

use LAG\AdminBundle\View\Helper\RenderHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class RenderExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('lag_admin_link', [RenderHelper::class, 'renderLink'], ['is_safe' => ['html']]),
            new TwigFunction('lag_admin_link_url', [RenderHelper::class, 'generateLinkUrl']),
            new TwigFunction('lag_admin_action', [RenderHelper::class, 'renderAction'], ['is_safe' => ['html']]),
        ];
    }
}

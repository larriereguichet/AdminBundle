<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Twig\Extension;

use LAG\AdminBundle\Twig\Runtime\RenderRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class RenderExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('lag_admin_link', [RenderRuntime::class, 'renderLink'], ['is_safe' => ['html']]),
        ];
    }
}

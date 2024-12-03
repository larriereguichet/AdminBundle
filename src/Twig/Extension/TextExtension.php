<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Twig\Extension;

use LAG\AdminBundle\View\Helper\TextHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class TextExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('lag_admin_pluralize', [TextHelper::class, 'pluralize']),
            new TwigFilter('lag_admin_rich_text', [TextHelper::class, 'richText'], ['is_safe' => ['html']]),
        ];
    }
}

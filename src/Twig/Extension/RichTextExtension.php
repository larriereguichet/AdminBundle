<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Twig\Extension;

use LAG\AdminBundle\Twig\Runtime\RichTextRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class RichTextExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('lag_admin_rich_text', [RichTextRuntime::class, 'renderRichText'], ['is_safe' => ['html']]),
        ];
    }
}

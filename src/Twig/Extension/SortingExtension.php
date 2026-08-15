<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Twig\Extension;

use LAG\AdminBundle\Twig\Runtime\SortingRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class SortingExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('lag_admin_sort_url', [SortingRuntime::class, 'generateSortUrl']),
        ];
    }
}

<?php

declare(strict_types=1);

namespace LAG\AdminBundle\View\Helper;

use Pagerfanta\PagerfantaInterface;
use Twig\Extension\RuntimeExtensionInterface;

final readonly class PaginationHelper implements RuntimeExtensionInterface
{
    public function isPager(mixed $pager): bool
    {
        return $pager instanceof PagerfantaInterface;
    }
}

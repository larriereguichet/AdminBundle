<?php

declare(strict_types=1);

namespace LAG\AdminBundle\View\Helper;

use Pagerfanta\PagerfantaInterface;

final readonly class PaginationHelper implements PaginationHelperInterface
{
    public function isPager(mixed $pager): bool
    {
        return $pager instanceof PagerfantaInterface;
    }
}

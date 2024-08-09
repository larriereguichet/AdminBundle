<?php

declare(strict_types=1);

namespace LAG\AdminBundle\View\Helper;

interface PaginationHelperInterface
{
    public function isPager(mixed $pager): bool;
}

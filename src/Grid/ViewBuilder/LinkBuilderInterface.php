<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\ViewBuilder;

use LAG\AdminBundle\Grid\View\CellView;
use LAG\AdminBundle\Metadata\Attribute\Link;

interface LinkBuilderInterface
{
    /**
     * @param array<string|mixed> $context
     */
    public function buildLink(Link $link, mixed $data, array $context = []): ?CellView;
}

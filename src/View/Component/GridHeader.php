<?php

declare(strict_types=1);

namespace LAG\AdminBundle\View\Component;

use LAG\AdminBundle\Grid\View\Header;

final class GridHeader
{
    public bool $sortable = false;
    public string $sort;
    public string $sortParameter;
    public string $order;
    public string $orderParameter;

    public function mount(Header $header): void
    {
        $this->sortable = $header->sortable;
        $this->sort = $header->sort;
        $this->sortParameter = $header->sortParameter;
        $this->order = $header->order;
        $this->orderParameter = $header->orderParameter;
    }
}

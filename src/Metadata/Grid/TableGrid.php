<?php

namespace LAG\AdminBundle\Metadata\Grid;

#[\Attribute(\Attribute::TARGET_CLASS && \Attribute::IS_REPEATABLE)]
class TableGrid extends Grid
{
    public function getTemplate(): string
    {
        return '@LAGAdmin/grids/table_grid.html.twig';
    }
}

<?php
declare(strict_types=1);

namespace LAG\AdminBundle\Grid\Builder;

use LAG\AdminBundle\Resource\Metadata\Grid;

/**
 * Build a single grid to be used in one or several resource collection view.
 */
interface GridBuilderInterface
{
    public function build(): Grid;
}

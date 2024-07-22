<?php
declare(strict_types=1);

namespace LAG\AdminBundle\Grid\Builder;

use LAG\AdminBundle\Resource\Metadata\Grid;

interface GridBuilderInterface
{
    public function build(): Grid;
}

<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Config;

use LAG\AdminBundle\Metadata\Grid;
use Symfony\Component\Config\Builder\ConfigBuilderInterface;

interface GridConfigInterface extends ConfigBuilderInterface
{
    public function addGrid(Grid $grid): self;
}

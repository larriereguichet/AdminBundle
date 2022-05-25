<?php

namespace LAG\AdminBundle\Grid\Factory;

use LAG\AdminBundle\Admin\Admin;
use LAG\AdminBundle\Grid\Grid;
use LAG\AdminBundle\Metadata\Action;

interface GridFactoryInterface
{
    public function create(mixed $data, Admin $admin, Action $action): Grid;
}

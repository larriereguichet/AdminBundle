<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Admin\Factory;

use LAG\AdminBundle\Metadata\Admin;

interface AdminFactoryInterface
{
    /**
     * Create a new Admin metadata instance.
     */
    public function create(Admin $resource): Admin;
}

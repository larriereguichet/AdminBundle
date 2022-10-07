<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Factory;

use LAG\AdminBundle\Metadata\AdminResource;

interface AdminFactoryInterface
{
    /**
     * Create a new Admin resource instance. The resource will be validated then returned. An event is dispatched before
     * the validation and another after.
     */
    public function create(AdminResource $resource): AdminResource;
}

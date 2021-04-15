<?php

namespace LAG\AdminBundle\Factory;

use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Exception\Exception;

interface AdminFactoryInterface
{
    /**
     * Create an Admin instance from the request parameters.
     *
     * @throws Exception
     */
    public function create(string $name, array $options = []): AdminInterface;
}

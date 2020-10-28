<?php

namespace LAG\AdminBundle\Factory;

use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;

interface AdminFactoryInterface
{
    /**
     * Create an Admin instance from the request parameters.
     *
     * @throws Exception
     */
    public function createFromRequest(Request $request): AdminInterface;

    /**
     * Return true if the current Request is supported. Supported means that the Request has the required valid
     * parameters to get an admin from the registry.
     */
    public function supports(Request $request): bool;
}

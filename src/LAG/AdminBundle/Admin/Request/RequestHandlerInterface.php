<?php

namespace LAG\AdminBundle\Admin\Request;

use Exception;
use LAG\AdminBundle\Admin\AdminInterface;
use Symfony\Component\HttpFoundation\Request;

interface RequestHandlerInterface
{
    /**
     * Return the admin according to the Request's attributes,
     *
     * @param Request $request
     *
     * @return AdminInterface
     *
     * @throws Exception
     */
    public function handle(Request $request);
    
    /**
     * Return true if the current Request is supported. Supported means that the Request has the required valid
     * parameters to get an admin from the registry.
     *
     * @param Request $request
     *
     * @return boolean
     */
    public function supports(Request $request);
}

<?php

namespace LAG\AdminBundle\Admin\Request;

use Exception;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Admin\Registry\Registry;
use LAG\AdminBundle\LAGAdminBundle;
use Symfony\Component\HttpFoundation\Request;

class RequestHandler
{
    /**
     * @var Registry
     */
    protected $registry;
    
    /**
     * RequestHandler constructor.
     *
     * @param Registry $registry
     */
    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }
    
    /**
     * @param Request $request
     *
     * @return AdminInterface
     *
     * @throws Exception
     */
    public function handle(Request $request)
    {
        $routeParameters = $request->get('_route_params');

        if (!is_array($routeParameters)) {
            throw new Exception(
                'Cannot find admin from request. _route_params parameters for request not found or invalid'
            );
        }
        if (!key_exists(LAGAdminBundle::REQUEST_PARAMETER_ADMIN, $routeParameters)) {
            throw new Exception('Cannot find admin from request. "_admin" route parameter is missing');
        }
        if (!key_exists(LAGAdminBundle::REQUEST_PARAMETER_ACTION, $routeParameters)) {
            throw new Exception('Cannot find admin action from request. "_action" route parameter is missing');
        }

        return $this
            ->registry
            ->get($routeParameters['_admin'])
        ;
    }
    
    /**
     * Return true if the current Request is supported. Supported means that the Request has the required valid
     * parameters to get an admin from the registry.
     *
     * @param Request $request
     *
     * @return boolean
     */
    public function supports(Request $request)
    {
        $routeParameters = $request->get('_route_params');
    
        if (!is_array($routeParameters)) {
            return false;
        }
    
        if (!key_exists(LAGAdminBundle::REQUEST_PARAMETER_ADMIN, $routeParameters) ||
            !key_exists(LAGAdminBundle::REQUEST_PARAMETER_ACTION, $routeParameters)
        ) {
            return false;
        }
    
        if (!$this->registry->has($routeParameters[LAGAdminBundle::REQUEST_PARAMETER_ADMIN])) {
            return false;
        }
        
        return true;
    }
}

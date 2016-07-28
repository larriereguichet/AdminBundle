<?php

namespace LAG\AdminBundle\Admin\Request;


use Exception;
use LAG\AdminBundle\Admin\Registry\Registry;
use Symfony\Component\HttpFoundation\Request;

class RequestHandler
{
    /**
     * @var Registry
     */
    protected $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    public function handle(Request $request)
    {
        $routeParameters = $request->get('_route_params');

        if (!$routeParameters) {
            throw new Exception('Cannot find admin from request. _route_params parameters for request not found');
        }
        if (!array_key_exists('_admin', $routeParameters)) {
            throw new Exception('Cannot find admin from request. "_admin" route parameter is missing');
        }
        if (!array_key_exists('_action', $routeParameters)) {
            throw new Exception('Cannot find admin action from request. "_action" route parameter is missing');
        }

        return $this
            ->registry
            ->get($routeParameters['_admin']);
    }
}

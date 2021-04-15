<?php

namespace LAG\AdminBundle\Request\Extractor;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\LAGAdminBundle;
use Symfony\Component\HttpFoundation\Request;

class ParametersExtractor implements ParametersExtractorInterface
{
    public function getAdminName(Request $request): string
    {
        if (!$this->supports($request)) {
            throw new Exception('No admin resource was found in the request. The route is wrongly configured');
        }

        return $request->get('_route_params')[LAGAdminBundle::REQUEST_PARAMETER_ADMIN];
    }

    public function getActionName(Request $request): string
    {
        if (!$this->supports($request)) {
            throw new Exception('No action resource was found in the request. The route is wrongly configured');
        }

        return $request->get('_route_params')[LAGAdminBundle::REQUEST_PARAMETER_ACTION];
    }

    public function supports(Request $request): bool
    {
        $routeParameters = $request->get('_route_params');

        if (!\is_array($routeParameters)) {
            return false;
        }

        if (
            empty($routeParameters[LAGAdminBundle::REQUEST_PARAMETER_ADMIN]) ||
            $routeParameters[LAGAdminBundle::REQUEST_PARAMETER_ADMIN] === null ||
            empty($routeParameters[LAGAdminBundle::REQUEST_PARAMETER_ACTION]) ||
            $routeParameters[LAGAdminBundle::REQUEST_PARAMETER_ACTION] === null
        ) {
            return false;
        }

        return true;
    }
}

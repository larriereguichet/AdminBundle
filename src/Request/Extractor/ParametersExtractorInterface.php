<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Request\Extractor;

use LAG\AdminBundle\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;

interface ParametersExtractorInterface
{
    /**
     * Return the admin name contained in the request parameters (_route_params).
     *
     * @throws Exception If no admin name can be found
     */
    public function getAdminName(Request $request): string;

    /**
     * Return the admin name contained in the request parameters (_route_params).
     *
     * @throws Exception If no action name can be found
     */
    public function getActionName(Request $request): string;

    /**
     * Return true if the current Request is supported. Supported means that the Request has the required valid
     * parameters to get an admin from the registry.
     */
    public function supports(Request $request): bool;
}

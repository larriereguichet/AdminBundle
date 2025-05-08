<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Request\Extractor;

use Symfony\Component\HttpFoundation\Request;

/**
 * Extract parameters from the given request to return the current resource and operation name. It should use the
 * container bundle parameter to allow to change the parameters names.
 */
interface ParametersExtractorInterface
{
    /**
     * Return the resource operation name from the request attributes.
     */
    public function getApplicationName(Request $request): ?string;

    /**
     * Return the resource name from the request attributes.
     */
    public function getResourceName(Request $request): ?string;

    /**
     * Return the resource operation name from the request attributes.
     */
    public function getOperationName(Request $request): ?string;

    /**
     * Return true if the given request is supported by the AdminBundle.
     */
    public function supports(Request $request): bool;
}

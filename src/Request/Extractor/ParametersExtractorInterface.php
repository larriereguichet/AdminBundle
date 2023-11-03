<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Request\Extractor;

use Symfony\Component\HttpFoundation\Request;

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
}

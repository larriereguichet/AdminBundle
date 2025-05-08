<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Request\Uri;

use LAG\AdminBundle\Metadata\OperationInterface;
use Symfony\Component\HttpFoundation\Request;

interface UrlVariablesExtractorInterface
{
    /**
     * @return array<string, string>
     */
    public function extractVariables(OperationInterface $operation, Request $request): array;
}

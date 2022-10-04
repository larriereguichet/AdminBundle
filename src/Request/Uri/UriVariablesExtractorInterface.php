<?php

namespace LAG\AdminBundle\Request\Uri;

use LAG\AdminBundle\Metadata\OperationInterface;
use Symfony\Component\HttpFoundation\Request;

interface UriVariablesExtractorInterface
{
    public function extractVariables(OperationInterface $operation, Request $request): array;
}

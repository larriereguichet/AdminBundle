<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Request\Uri;

use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use Symfony\Component\HttpFoundation\Request;

class UriVariablesExtractor implements UriVariablesExtractorInterface
{
    public function extractVariables(OperationInterface $operation, Request $request): array
    {
        $uriVariables = [];

        foreach ($operation->getIdentifiers() as $identifier) {
            if ($request->attributes->has($identifier)) {
                $uriVariables[$identifier] = $request->attributes->get($identifier);
            }

            if ($request->query->has($identifier)) {
                $uriVariables[$identifier] = $request->query->get($identifier);
            }
        }

        return $uriVariables;
    }
}

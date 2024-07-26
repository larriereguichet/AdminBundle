<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Request\Extractor;

use Symfony\Component\HttpFoundation\Request;

final readonly class ResourceParametersExtractor implements ResourceParametersExtractorInterface
{
    public function __construct(
        private string $applicationParameter,
        private string $resourceParameter,
        private string $operationParameter,
    ) {
    }

    public function getApplicationName(Request $request): ?string
    {
        return $request->attributes->get($this->applicationParameter);
    }

    public function getResourceName(Request $request): ?string
    {
        return $request->attributes->get($this->resourceParameter);
    }

    public function getOperationName(Request $request): ?string
    {
        return $request->attributes->get($this->operationParameter);
    }
}

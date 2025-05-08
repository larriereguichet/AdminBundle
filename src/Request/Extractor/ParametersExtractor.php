<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Request\Extractor;

use Symfony\Component\HttpFoundation\Request;

use function Symfony\Component\String\u;

final readonly class ParametersExtractor implements ParametersExtractorInterface
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
        $applicationName = $this->getApplicationName($request);
        $resourceName = $request->attributes->get($this->resourceParameter);

        if ($applicationName === null || $resourceName === null) {
            return null;
        }

        return u('.')->join([$applicationName, $resourceName])->toString();
    }

    public function getOperationName(Request $request): ?string
    {
        $resourceName = $this->getResourceName($request);
        $operationName = $request->attributes->get($this->operationParameter);

        if ($resourceName === null || $operationName === null) {
            return null;
        }

        return u('.')->join([$resourceName, $operationName])->toString();
    }

    public function supports(Request $request): bool
    {
        return $this->getOperationName($request) !== null;
    }
}

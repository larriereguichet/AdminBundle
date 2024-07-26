<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Context;

use LAG\AdminBundle\Exception\ResourceNotFoundException;
use LAG\AdminBundle\Request\Extractor\ResourceParametersExtractorInterface;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use LAG\AdminBundle\Resource\Metadata\Resource;
use LAG\AdminBundle\Resource\Registry\ResourceRegistryInterface;
use Symfony\Component\HttpFoundation\Request;

final readonly class ResourceContext implements ResourceContextInterface
{
    public function __construct(
        private ResourceParametersExtractorInterface $parametersExtractor,
        private ResourceRegistryInterface $resourceRegistry,
    ) {
    }

    public function getOperation(Request $request): OperationInterface
    {
        if (!$this->supports($request)) {
            throw new ResourceNotFoundException('The current request is not supported by any admin resource');
        }
        $applicationName = $this->parametersExtractor->getApplicationName($request);
        $resourceName = $this->parametersExtractor->getResourceName($request);
        $operationName = $this->parametersExtractor->getOperationName($request);
        $resource = $this->resourceRegistry->get($resourceName, $applicationName);

        return $resource->getOperation($operationName)->withResource($resource);
    }

    public function getResource(Request $request): Resource
    {
        return $this->getOperation($request)->getResource();
    }

    public function supports(Request $request): bool
    {
        if ($this->parametersExtractor->getApplicationName($request) === null) {
            return false;
        }

        if ($this->parametersExtractor->getResourceName($request) === null) {
            return false;
        }

        if ($this->parametersExtractor->getOperationName($request) === null) {
            return false;
        }

        return true;
    }
}

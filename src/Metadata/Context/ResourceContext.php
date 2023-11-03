<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Context;

use LAG\AdminBundle\Exception\ResourceNotFoundException;
use LAG\AdminBundle\Metadata\AdminResource;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Metadata\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Request\Extractor\ParametersExtractorInterface;
use Symfony\Component\HttpFoundation\Request;

class ResourceContext implements ResourceContextInterface
{
    public function __construct(
        private ParametersExtractorInterface $parametersExtractor,
        private ResourceRegistryInterface $resourceRegistry,
    ) {
    }

    public function getOperation(Request $request): OperationInterface
    {
        if (!$this->supports($request)) {
            throw new ResourceNotFoundException('The current request is not supported by any admin resource');
        }
        $resourceName = $this->parametersExtractor->getResourceName($request);
        $operationName = $this->parametersExtractor->getOperationName($request);
        $resource = $this->resourceRegistry->get($resourceName);

        return $resource->getOperation($operationName);
    }

    public function getResource(Request $request): AdminResource
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

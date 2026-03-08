<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Context;

use LAG\AdminBundle\Exception\UnsupportedRequestException;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Metadata\ResourceInterface;
use LAG\AdminBundle\Request\Extractor\ParametersExtractorInterface;
use LAG\AdminBundle\Resource\Factory\ResourceFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final readonly class ResourceContext implements ResourceContextInterface
{
    public function __construct(
        private RequestStack $requestStack,
        private ParametersExtractorInterface $parametersExtractor,
        private ResourceFactoryInterface $resourceFactory,
    ) {
    }

    public function getResource(): ResourceInterface
    {
        $resourceName = $this->parametersExtractor->getResourceName($this->getRequest());

        if ($resourceName === null) {
            throw new UnsupportedRequestException('The current request is not supported by any resource');
        }

        return $this->resourceFactory->create($resourceName);
    }

    public function hasResource(): bool
    {
        return $this->parametersExtractor->getResourceName($this->getRequest()) !== null;
    }

    public function getOperation(): OperationInterface
    {
        $operationName = $this->parametersExtractor->getOperationName($this->getRequest());

        if ($operationName === null) {
            throw new UnsupportedRequestException('The current request is not supported by any resource or operation');
        }

        return $this->getResource()->getOperation($operationName);
    }

    public function hasOperation(): bool
    {
        return $this->parametersExtractor->getOperationName($this->getRequest()) !== null;
    }

    private function getRequest(): Request
    {
        $request = $this->requestStack->getCurrentRequest();

        if ($request === null) {
            throw new UnsupportedRequestException('Unable to find a current request');
        }

        return $request;
    }
}

<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Context;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Metadata\Resource;
use LAG\AdminBundle\Request\Extractor\ParametersExtractorInterface;
use LAG\AdminBundle\Resource\Factory\ResourceFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final readonly class ResourceContext implements ResourceContextInterface
{
    public function __construct(
        private RequestStack $requestStack,
        private ParametersExtractorInterface $parametersExtractor,
        private ResourceFactoryInterface $resourceFactory,
    ) {
    }

    public function getResource(): Resource
    {
        $request = $this->requestStack->getCurrentRequest();
        $resourceName = $this->parametersExtractor->getResourceName($request);

        if ($resourceName === null) {
            throw new Exception('The current request is not supported by any resource');
        }

        return $this->resourceFactory->create($resourceName);
    }

    public function hasResource(): bool
    {
        $request = $this->requestStack->getCurrentRequest();

        return $this->parametersExtractor->getResourceName($request) !== null;
    }
}

<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Request\Resolver;

use LAG\AdminBundle\Metadata\AdminResource;
use LAG\AdminBundle\Request\Extractor\ParametersExtractorInterface;
use LAG\AdminBundle\Resource\Registry\ResourceRegistryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class AdminValueResolver implements ValueResolverInterface
{
    public function __construct(
        private ParametersExtractorInterface $extractor,
        private ResourceRegistryInterface $resourceRegistry,
    ) {
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return $argument->getType() === AdminResource::class && $this->extractor->supports($request);
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $resourceName = $this->extractor->getResourceName($request);
        $operationName = $this->extractor->getOperationName($request);

        $this->resourceRegistry->load();
        $resource = $this->resourceRegistry->get($resourceName);
        $operation = $resource->getOperation($operationName);

        yield $resource->withCurrentOperation($operation);
    }
}

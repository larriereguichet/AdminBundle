<?php

namespace LAG\AdminBundle\Request\Resolver;

use LAG\AdminBundle\Admin\Factory\AdminFactoryInterface;
use LAG\AdminBundle\Metadata\Admin;
use LAG\AdminBundle\Request\Extractor\ParametersExtractorInterface;
use LAG\AdminBundle\Resource\Registry\ResourceRegistryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class AdminArgumentValueResolver implements ArgumentValueResolverInterface
{
    public function __construct(
        private ParametersExtractorInterface $extractor,
        private ResourceRegistryInterface $resourceRegistry,
    ) {
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return $argument->getType() === Admin::class && $this->extractor->supports($request);
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

<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Request\Resolver;

use LAG\AdminBundle\Metadata\AdminResource;
use LAG\AdminBundle\Metadata\Context\ResourceContextInterface;
use LAG\AdminBundle\Metadata\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Request\Extractor\ParametersExtractorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class ResourceValueResolver implements ValueResolverInterface
{
    public function __construct(
        private ResourceContextInterface $resourceContext,
    ) {
    }

    /** @return iterable<AdminResource> */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (!$this->supports($request, $argument)) {
            return [];
        }

        yield $this->resourceContext->getResource($request);
    }

    private function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return $argument->getType() === AdminResource::class && $this->resourceContext->supports($request);
    }
}

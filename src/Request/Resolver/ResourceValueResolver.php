<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Request\Resolver;

use LAG\AdminBundle\Metadata\Resource;
use LAG\AdminBundle\Resource\Context\ResourceContextInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

final readonly class ResourceValueResolver implements ValueResolverInterface
{
    public function __construct(
        private ResourceContextInterface $resourceContext,
    ) {
    }

    /** @return iterable<resource> */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (!$this->supports($request, $argument)) {
            return [];
        }

        yield $this->resourceContext->getResource($request);
    }

    private function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return $argument->getType() === Resource::class && $this->resourceContext->supports($request);
    }
}

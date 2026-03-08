<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Request\ValueResolver;

use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Resource\Context\ResourceContextInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

final readonly class OperationValueResolver implements ValueResolverInterface
{
    public function __construct(
        private ResourceContextInterface $resourceContext,
    ) {
    }

    /** @return iterable<int, OperationInterface> */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (!$this->resourceContext->hasOperation()) {
            return [];
        }

        if (!is_a($argument->getType(), OperationInterface::class, true)) {
            return [];
        }

        yield $this->resourceContext->getOperation();
    }
}

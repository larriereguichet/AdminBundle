<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Request\Resolver;

use LAG\AdminBundle\Resource\Context\ResourceContextInterface;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

final readonly class OperationValueResolver implements ValueResolverInterface
{
    public function __construct(
        private ResourceContextInterface $resourceContext,
    ) {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (!$this->resourceContext->supports($request)
            || $argument->getType() === null
            || (!class_exists($argument->getType()) && !interface_exists($argument->getType()))
        ) {
            return [];
        }
        $interfaces = class_implements($argument->getType(), false);

        if ($interfaces === false
            || (!\in_array(OperationInterface::class, $interfaces) && $argument->getType() !== OperationInterface::class)
        ) {
            return [];
        }

        yield $this->resourceContext->getOperation($request);
    }
}

<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Request\ValueResolver;

use LAG\AdminBundle\Grid\Factory\GridFactoryInterface;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\GridInterface;
use LAG\AdminBundle\Resource\Context\OperationContextInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

final readonly class GridValueResolver implements ValueResolverInterface
{
    public function __construct(
        private OperationContextInterface $operationContext,
        private GridFactoryInterface $gridMetadataFactory,
    ) {
    }

    /** @return iterable<int, GridInterface> */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (!$this->operationContext->hasOperation()) {
            return [];
        }

        if (!is_a($argument->getType(), GridInterface::class, true)) {
            return [];
        }
        $operation = $this->operationContext->getOperation();

        if (!$operation instanceof CollectionOperationInterface || $operation->getGrid() === null) {
            return [];
        }

        yield $this->gridMetadataFactory->create($operation->getGrid(), $operation);
    }
}

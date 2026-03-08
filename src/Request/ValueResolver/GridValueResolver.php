<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Request\ValueResolver;

use LAG\AdminBundle\Grid\Factory\GridFactoryInterface;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\GridInterface;
use LAG\AdminBundle\Resource\Context\ResourceContextInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

final readonly class GridValueResolver implements ValueResolverInterface
{
    public function __construct(
        private ResourceContextInterface $resourceContext,
        private GridFactoryInterface $gridFactory,
    ) {
    }

    /** @return iterable<int, GridInterface> */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (!$this->resourceContext->hasOperation()) {
            return [];
        }

        if (!is_a($argument->getType(), GridInterface::class, true)) {
            return [];
        }
        $operation = $this->resourceContext->getOperation();

        if (!$operation instanceof CollectionOperationInterface || $operation->getGrid() === null) {
            return [];
        }

        yield $this->gridFactory->create($operation->getGrid(), $operation);
    }
}

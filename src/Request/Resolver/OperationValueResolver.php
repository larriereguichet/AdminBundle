<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Request\Resolver;

use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Request\Extractor\ParametersExtractorInterface;
use LAG\AdminBundle\Resource\Context\OperationContextInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

final readonly class OperationValueResolver implements ValueResolverInterface
{
    public function __construct(
        private ParametersExtractorInterface $parametersExtractor,
        private OperationContextInterface $operationContext,
    ) {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (!$this->supports($request, $argument) || !$this->supportsClass($argument->getType())) {
            return [];
        }

        yield $this->operationContext->getOperation();
    }

    private function supports(Request $request, ArgumentMetadata $argument): bool
    {
        if (!$this->parametersExtractor->supports($request)) {
            return false;
        }

        if ($argument->getType() === null) {
            return false;
        }

        if (!class_exists($argument->getType()) && !interface_exists($argument->getType())) {
            return false;
        }

        return true;
    }

    private function supportsClass(string $class): bool
    {
        $interfaces = class_implements($class, false);

        if ($interfaces === false) {
            return false;
        }

        return \in_array(OperationInterface::class, $interfaces) || $class === OperationInterface::class;
    }
}

<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Context;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Request\Extractor\ParametersExtractorInterface;
use LAG\AdminBundle\Resource\Factory\OperationFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final readonly class OperationContext implements OperationContextInterface
{
    public function __construct(
        private RequestStack $requestStack,
        private ParametersExtractorInterface $parametersExtractor,
        private OperationFactoryInterface $operationFactory,
    ) {
    }

    public function getOperation(): OperationInterface
    {
        $request = $this->requestStack->getCurrentRequest();
        $operationName = $this->parametersExtractor->getOperationName($request);

        if ($operationName === null) {
            throw new Exception('The current request is not supported by any resource or operation');
        }

        return $this->operationFactory->create($operationName);
    }

    public function hasOperation(): bool
    {
        $request = $this->requestStack->getCurrentRequest();

        return $this->parametersExtractor->getOperationName($request) !== null;
    }
}

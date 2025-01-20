<?php

declare(strict_types=1);

namespace LAG\AdminBundle\State\Provider;

use LAG\AdminBundle\Request\ContextBuilder\ContextBuilderInterface;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final readonly class ContextProvider implements ProviderInterface
{
    public function __construct(
        private ProviderInterface $provider,
        private RequestStack $requestStack,
        private ContextBuilderInterface $contextBuilder,
    ) {
    }

    public function provide(OperationInterface $operation, array $urlVariables = [], array $context = []): mixed
    {
        $request = $this->requestStack->getCurrentRequest();
        $context += $this->contextBuilder->buildContext($operation, $request);

        return $this->provider->provide($operation, $urlVariables, $context);
    }
}
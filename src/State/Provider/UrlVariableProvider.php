<?php

declare(strict_types=1);

namespace LAG\AdminBundle\State\Provider;

use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Request\Uri\UrlVariablesExtractorInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final readonly class UrlVariableProvider implements ProviderInterface
{
    public function __construct(
        private ProviderInterface $provider,
        private RequestStack $requestStack,
        private UrlVariablesExtractorInterface $urlVariablesExtractor,
    ) {
    }

    public function provide(OperationInterface $operation, array $urlVariables = [], array $context = []): mixed
    {
        $request = $this->requestStack->getCurrentRequest();
        $urlVariables += $this->urlVariablesExtractor->extractVariables($operation, $request);

        return $this->provider->provide($operation, $urlVariables, $context);
    }
}

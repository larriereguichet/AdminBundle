<?php

namespace LAG\AdminBundle\View\Helper;

use LAG\AdminBundle\Resource\Context\ResourceContextInterface;
use LAG\AdminBundle\Routing\UrlGenerator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\RequestStack;

readonly class RoutingHelper
{
    public function __construct(
        private ResourceContextInterface $context,
        private RequestStack $requestStack,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function generateOperationPath(
        string $resource,
        string $operation,
        mixed $data = null,
        string $applicationName = null,
    ): string
    {
        if ($applicationName === null) {
            $request = $this->requestStack->getCurrentRequest();
            $applicationName = $this->context->getOperation($request)->getResource()->getApplication();
        }

        return $this->urlGenerator->generateFromOperationName($resource, $operation, $data, $applicationName);
    }
}

<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Response\Handler;

use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Routing\UrlGenerator\ResourceUrlGeneratorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

final readonly class RedirectResponseHandler implements RedirectResponseHandlerInterface
{
    public function __construct(
        private ResourceUrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function createRedirectResponse(OperationInterface $operation, mixed $data, array $context = []): RedirectResponse
    {
        $targetUrl = match (true) {
            $operation->getRedirectOperation() !== null => $this->urlGenerator->generateFromOperationName(
                $operation->getRedirectOperation(),
                $data,
            ),
            $operation->getRedirectRoute() !== null => $this->urlGenerator->generateFromRouteName(
                $operation->getRedirectRoute(),
                $operation->getRedirectRouteParameters(),
                $data,
            ),
            default => $this->urlGenerator->generate($operation, $data),
        };

        return new RedirectResponse($targetUrl, $context['responseCode'] ?? Response::HTTP_FOUND);
    }
}

<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Response\Handler;

use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use LAG\AdminBundle\Routing\UrlGenerator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class RedirectRespondHandler implements ResponseHandlerInterface
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function supports(OperationInterface $operation, mixed $data, Request $request, array $context = []): bool
    {
        return ($context['submitted'] ?? false) && $request->query->getBoolean('_partial') !== true;
    }

    public function createResponse(OperationInterface $operation, mixed $data, Request $request, array $context = []): Response
    {
        if ($operation->getRedirectOperation()) {
            $redirectUrl = $this->urlGenerator->generateFromOperationName(
                $operation->getRedirectResource() ?? $operation->getResource()->getName(),
                $operation->getRedirectOperation(),
                $data,
                $operation->getRedirectApplication() ?? $operation->getResource()->getApplication(),
            );

            return new RedirectResponse($redirectUrl);
        }

        if ($operation->getRedirectRoute()) {
            $redirectUrl = $this->urlGenerator->generateFromRouteName(
                $operation->getRedirectRoute(),
                $operation->getRedirectRouteParameters(),
                $data,
            );

            return new RedirectResponse($redirectUrl);
        }
        $redirectUrl = $this->urlGenerator->generateFromRouteName(
            $operation->getRoute(),
            $operation->getRouteParameters(),
            $data,
        );

        return new RedirectResponse($redirectUrl);
    }
}
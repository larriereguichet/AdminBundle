<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Response\Handler;

use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Resource\Factory\OperationFactoryInterface;
use LAG\AdminBundle\Routing\UrlGenerator\OperationUrlGeneratorInterface;
use LAG\AdminBundle\Routing\UrlGenerator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

final readonly class RedirectResponseHandler implements RedirectResponseHandlerInterface
{
    public function __construct(
        private OperationFactoryInterface $operationFactory,
        private OperationUrlGeneratorInterface $operationUrlGenerator,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function createRedirectResponse(
        Request $request,
        OperationInterface $operation,
        mixed $data,
        array $context = []
    ): RedirectResponse {
        if ($operation->getRedirectOperation() !== null) {
            $targetOperation = $this->operationFactory->create($operation->getRedirectOperation());

            return new RedirectResponse($this->operationUrlGenerator->generateUrl($targetOperation));
        }

        if ($operation->getRedirectRoute() !== null) {
            return new RedirectResponse($this->urlGenerator->generateUrl(
                $operation->getRedirectRoute(),
                $operation->getRedirectRouteParameters(),
                $data,
            ));
        }

        return new RedirectResponse($this->operationUrlGenerator->generateUrl($operation, $data));
    }
}

<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Routing\UrlGenerator;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Metadata\Attribute\Link;
use LAG\AdminBundle\Resource\Factory\OperationFactoryInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface as SymfonyUrlGeneratorInterface;

final readonly class LinkUrlGenerator implements LinkUrlGeneratorInterface
{
    public function __construct(
        private OperationUrlGeneratorInterface $operationUrlGenerator,
        private UrlGeneratorInterface $urlGenerator,
        private OperationFactoryInterface $operationFactory,
    ) {
    }

    public function generateUrl(Link $link, mixed $data = null, int $referenceType = SymfonyUrlGeneratorInterface::ABSOLUTE_PATH): string
    {
        if ($link->getUrl() !== null) {
            return $link->getUrl();
        }

        if ($link->getOperation() !== null) {
            $operation = $this->operationFactory->create($link->getOperation());

            return $this->operationUrlGenerator->generateUrl($operation, $data, $referenceType);
        }

        if ($link->getRoute() !== null) {
            return $this->urlGenerator->generateUrl(
                $link->getRoute(),
                $link->getRouteParameters(),
                $data,
                $referenceType,
            );
        }

        throw new Exception('Unable to generate a route for an action');
    }
}

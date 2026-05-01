<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Bridge\KnpMenu\Extension;

use Knp\Menu\Factory\ExtensionInterface;
use Knp\Menu\ItemInterface;
use LAG\AdminBundle\Resource\Factory\OperationFactoryInterface;
use LAG\AdminBundle\Routing\UrlGenerator\OperationUrlGeneratorInterface;
use LAG\AdminBundle\Routing\UrlGenerator\UrlGeneratorInterface;

final readonly class ResourceExtension implements ExtensionInterface
{
    public function __construct(
        private OperationFactoryInterface $operationFactory,
        private UrlGeneratorInterface $urlGenerator,
        private OperationUrlGeneratorInterface $operationUrlGenerator,
    ) {
    }

    public function buildOptions(array $options): array
    {
        if (!isset($options['operation'])) {
            return $options;
        }
        $operation = $this->operationFactory->create($options['operation']);
        $resource = $operation->getResource();

        if (!empty($options['routeParameters'])) {
            $options['uri'] = $this->urlGenerator->generateUrl($operation->getRoute(), $options['routeParameters']);
        } else {
            $options['uri'] = $this->operationUrlGenerator->generateUrl($operation);
        }

        if (!isset($options['extras'])) {
            $options['extras'] = [];
        }

        if (empty($options['label'])) {
            $options['label'] = $operation->getTitle();
        }

        if (!isset($options['extras']['translation_domain']) && $resource->getTranslationDomain() !== null) {
            $options['extras']['translation_domain'] = $resource->getTranslationDomain();
        }

        return $options;
    }

    public function buildItem(ItemInterface $item, array $options): void
    {
    }
}

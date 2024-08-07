<?php

namespace LAG\AdminBundle\Bridge\KnpMenu\Extension;

use Knp\Menu\Factory\ExtensionInterface;
use Knp\Menu\ItemInterface;
use LAG\AdminBundle\Resource\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Routing\UrlGenerator\UrlGeneratorInterface;

final readonly class ResourceExtension implements ExtensionInterface
{
    public function __construct(
        private ResourceRegistryInterface $registry,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function buildOptions(array $options): array
    {
        if (!isset($options['resource']) || !isset($options['operation'])) {
            return $options;
        }

        if (!$this->registry->has($options['resource'], $options['application'] ?? null)) {
            return $options;
        }
        $resource = $this->registry->get($options['resource'], $options['application'] ?? null);


        if (!$resource->hasOperation($options['operation'])) {
            return $options;
        }
        $operation = $resource->getOperation($options['operation']);
        $options['uri'] = $this->urlGenerator->generateOperationUrl($operation);
        $options['extras'] = [];

        if (empty($options['label'])) {
            $options['label'] = $operation->getTitle();
        }

        return $options;
    }

    public function buildItem(ItemInterface $item, array $options): void
    {
    }
}

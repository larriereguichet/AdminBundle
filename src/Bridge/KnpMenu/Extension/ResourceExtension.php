<?php

namespace LAG\AdminBundle\Bridge\KnpMenu\Extension;

use Knp\Menu\Factory\ExtensionInterface;
use Knp\Menu\ItemInterface;
use LAG\AdminBundle\Metadata\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Routing\UrlGenerator\UrlGeneratorInterface;

class ResourceExtension implements ExtensionInterface
{
    public function __construct(
        private ResourceRegistryInterface $registry,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function buildOptions(array $options): array
    {
       $options = ['application' => null,'resource' => null, 'operation' => null] + $options;

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
        $options['uri'] = $this->urlGenerator->generate($resource->getOperation($options['operation']));
        $options['extras'] = [

        ];

        return $options;
    }

    public function buildItem(ItemInterface $item, array $options): void
    {
    }
}

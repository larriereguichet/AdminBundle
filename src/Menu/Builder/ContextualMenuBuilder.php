<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Menu\Builder;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use LAG\AdminBundle\Resource\Context\ResourceContextInterface;
use LAG\AdminBundle\Resource\Metadata\Link;
use LAG\AdminBundle\Resource\Metadata\Resource;
use LAG\AdminBundle\Resource\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Routing\Route\RouteNameGeneratorInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final readonly class ContextualMenuBuilder
{
    public function __construct(
        private ResourceContextInterface $resourceContext,
        private ResourceRegistryInterface $registry,
        private RequestStack $requestStack,
        private RouteNameGeneratorInterface $routeNameGenerator,
        private FactoryInterface $factory,
    ) {
    }

    public function build(array $options = []): ItemInterface
    {
        $request = $this->requestStack->getCurrentRequest();
        $menu = $this->factory->createItem('root', $options);

        if (!$this->resourceContext->supports($request)) {
            return $menu;
        }
        $operation = $this->resourceContext->getOperation($request);
        $resource = $operation->getResource();

        foreach ($operation->getContextualActions() as $link) {
            $menu->addChild($link->getText(), $this->buildItemOptions($resource, $link));
        }

        return $menu;
    }

    private function buildItemOptions(Resource $resource, Link $link): array
    {
        $contextualResource = $resource;

        // TODO use application instead
        if ($link->getResource() !== $resource->getName()) {
            $contextualResource = $this->registry->get($link->getResource());
        }
        $contextualOperation = $contextualResource->getOperation($link->getOperation());
        $options = [];

        if ($link->getUrl()) {
            $options['url'] = $link->getUrl();
        } elseif ($link->getRoute()) {
            $options['route'] = $link->getRoute();
        } else {
            $options['route'] = $this
                ->routeNameGenerator
                ->generateRouteName($resource, $contextualOperation)
            ;
        }

        if ($link->getIcon()) {
            $options['extras']['icon'] = $link->getIcon();
        } else {
            $icon = match ($link->getOperation()) {
                'create' => 'plus-lg',
                'update' => 'pencil-lg',
                'delete' => 'cross-lg',
                default => null,
            };

            if ($icon) {
                $options['extras']['icon'] = $icon;
            }
        }

        return $options;
    }
}

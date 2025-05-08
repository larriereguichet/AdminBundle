<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Menu\Builder;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use LAG\AdminBundle\Metadata\Link;
use LAG\AdminBundle\Resource\Context\OperationContextInterface;
use LAG\AdminBundle\Resource\Factory\OperationFactoryInterface;
use LAG\AdminBundle\Routing\Route\RouteNameGeneratorInterface;

final readonly class ContextualMenuBuilder
{
    public function __construct(
        private OperationContextInterface $operationContext,
        private OperationFactoryInterface $operationFactory,
        private RouteNameGeneratorInterface $routeNameGenerator,
        private FactoryInterface $factory,
    ) {
    }

    public function build(array $options = []): ItemInterface
    {
        $menu = $this->factory->createItem('root', $options);

        if (!$this->operationContext->hasOperation()) {
            return $menu;
        }
        $operation = $this->operationContext->getOperation();

        foreach ($operation->getContextualActions() as $link) {
            $menu->addChild($link->getText(), $this->buildItemOptions($link));
        }

        return $menu;
    }

    private function buildItemOptions(Link $link): array
    {
        $contextualOperation = $this->operationFactory->create($link->getOperation());
        $options = [];

        if ($link->getUrl()) {
            $options['url'] = $link->getUrl();
        } elseif ($link->getRoute()) {
            $options['route'] = $link->getRoute();
        } else {
            $options['route'] = $this
                ->routeNameGenerator
                ->generateRouteName($contextualOperation->getResource(), $contextualOperation)
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

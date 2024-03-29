<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Bridge\KnpMenu\Builder;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use LAG\AdminBundle\Metadata\AdminResource;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Metadata\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Request\Extractor\ParametersExtractorInterface;
use LAG\AdminBundle\Routing\Route\RouteNameGeneratorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ContextualMenuBuilder extends AbstractMenuBuilder
{
    public function __construct(
        private ParametersExtractorInterface $parametersExtractor,
        private ResourceRegistryInterface $registry,
        private RequestStack $requestStack,
        private RouteNameGeneratorInterface $routeNameGenerator,
        FactoryInterface $factory,
        EventDispatcherInterface $eventDispatcher,
    ) {
        parent::__construct($factory, $eventDispatcher);
    }

    public function getName(): string
    {
        return 'contextual';
    }

    protected function buildMenu(ItemInterface $menu): void
    {
        $request = $this->requestStack->getMainRequest();

        if (!$this->parametersExtractor->supports($request)) {
            return;
        }
        $resource = $this->getResource($request);
        $operation = $this->getOperation($resource, $request);

        foreach ($operation->getContextualActions() as $link) {
            $contextualResource = $resource;

            if ($link->getResourceName() !== $resource->getName()) {
                $contextualResource = $this->registry->get($link->getResourceName());
            }
            $contextualOperation = $contextualResource->getOperation($link->getOperationName());
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
                $icon = match ($link->getOperationName()) {
                    'create' => 'plus-lg',
                    'update' => 'pencil-lg',
                    'delete' => 'cross-lg',
                    default => null,
                };

                if ($icon) {
                    $options['extras']['icon'] = $icon;
                }
            }
            $menu->addChild($link->getLabel(), $options);
        }
    }

    private function getResource(Request $request): AdminResource
    {
        $resourceName = $this->parametersExtractor->getResourceName($request);

        return $this->registry->get($resourceName);
    }

    private function getOperation(AdminResource $resource, Request $request): OperationInterface
    {
        $operationName = $this->parametersExtractor->getOperationName($request);

        return $resource->getOperation($operationName);
    }
}

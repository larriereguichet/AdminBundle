<?php

namespace LAG\AdminBundle\Bridge\KnpMenu\Builder;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use LAG\AdminBundle\Admin\Factory\AdminConfigurationFactoryInterface;
use LAG\AdminBundle\Admin\Resource\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Routing\Route\RouteNameGeneratorInterface;
use LAG\AdminBundle\Translation\Helper\TranslationHelperInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use function Symfony\Component\String\u;

class LeftMenuBuilder
{
    use MenuBuilderTrait;

    public function __construct(
        private FactoryInterface $factory,
        private ResourceRegistryInterface $resourceRegistry,
        private TranslationHelperInterface $translationHelper,
        private AdminConfigurationFactoryInterface $adminConfigurationFactory,
        private RouteNameGeneratorInterface $routeNameGenerator,
        private EventDispatcherInterface $eventDispatcher,
    )
    {
    }

    public function createMenu(array $options = []): ItemInterface
    {
        $menu = $this->factory->createItem('root');

        foreach ($this->resourceRegistry->all() as $resource) {
            $configuration = $this
                ->adminConfigurationFactory
                ->create($resource->getName(), $resource->getConfiguration())
            ;

            if (!$configuration->hasAction('index')) {
                continue;
            }
            $translationKey = u($resource->getName())->snake()->toString();
            $menu
                ->addChild($resource->getName(), [
                    'route' => $this->routeNameGenerator->generateRouteName($resource->getName(), 'index'),
                ])
                ->setLabel('lag_admin.menu.'.$translationKey)
            ;
        }
        $this->dispatchMenuEvents('left', $menu);


        return $menu;
    }
}

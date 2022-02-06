<?php

namespace LAG\AdminBundle\Bridge\KnpMenu\Builder;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use LAG\AdminBundle\Admin\Resource\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Factory\Configuration\ConfigurationFactoryInterface;
use LAG\AdminBundle\Routing\Route\RouteNameGeneratorInterface;
use LAG\AdminBundle\Translation\Helper\TranslationHelperInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use function Symfony\Component\String\u;

class LeftMenuBuilder implements MenuBuilderInterface
{
    use MenuBuilderTrait;

    public function __construct(
        private FactoryInterface $factory,
        private ResourceRegistryInterface $resourceRegistry,
        private TranslationHelperInterface $translationHelper,
        private ConfigurationFactoryInterface $configurationFactory,
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
                ->configurationFactory
                ->createAdminConfiguration($resource->getName(), $resource->getConfiguration())
            ;

            if (!$configuration->hasAction('list')) {
                continue;
            }
            $translationKey = u($resource->getName())->snake()->toString();
            $menu->addChild($this->translationHelper->getTranslationKey($translationKey), [
                'route' => $this->routeNameGenerator->generateRouteName($resource->getName(), 'list'),
            ]);
        }
        $this->dispatchMenuEvents('left', $menu);


        return $menu;
    }
}

<?php

namespace LAG\AdminBundle\Bridge\KnpMenu\Builder;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use LAG\AdminBundle\Admin\Helper\AdminContextInterface;
use LAG\AdminBundle\Routing\Route\RouteNameGeneratorInterface;
use LAG\AdminBundle\Translation\Helper\TranslationHelperInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class TopMenuBuilder implements MenuBuilderInterface
{
    use MenuBuilderTrait;

    public function __construct(
        private FactoryInterface $factory,
        private AdminContextInterface $adminContext,
        private RouteNameGeneratorInterface $routeNameGenerator,
        private TranslationHelperInterface $translationHelper,
        private EventDispatcherInterface $eventDispatcher,
    )
    {
    }

    public function createMenu(array $options = []): ItemInterface
    {
        $menu = $this->factory->createItem('root', $options);

        if (!$this->adminContext->hasAdmin()) {
            return $menu;
        }
        $admin = $this->adminContext->getAdmin();
        $action = $admin->getAction()->getName();

        if ($action === 'list') {
            if ($admin->getConfiguration()->hasAction('create')) {
                $menu->addChild($this->translationHelper->getTranslationKey('create', $admin->getName()), [
                    'route' => $this->routeNameGenerator->generateRouteName($admin->getName(), 'create'),
                    'extras' => [
                        'icon' => 'plus'
                    ],
                ]);
            }
        }
        $this->dispatchMenuEvents('top', $menu);

        return $menu;
    }
}

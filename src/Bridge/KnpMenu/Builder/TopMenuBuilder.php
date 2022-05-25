<?php

namespace LAG\AdminBundle\Bridge\KnpMenu\Builder;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use LAG\AdminBundle\Admin\Context\AdminContextInterface;
use LAG\AdminBundle\Routing\Route\RouteNameGeneratorInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use function Symfony\Component\String\u;

class TopMenuBuilder
{
    use MenuBuilderTrait;

    public function __construct(
        private FactoryInterface $factory,
        private AdminContextInterface $adminContext,
        private RouteNameGeneratorInterface $routeNameGenerator,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function createMenu(array $options = []): ItemInterface
    {
        $menu = $this->factory->createItem('root', $options);

        if (!$this->adminContext->hasAdmin()) {
            return $menu;
        }
        $admin = $this->adminContext->getAdmin();
        $action = $admin->getAction();

        if ($action->getName() === 'index') {
            if ($admin->getConfiguration()->hasAction('create')) {
                $menu
                    ->addChild($action->getName(), [
                        'route' => $this->routeNameGenerator->generateRouteName($admin->getName(), 'create'),
                        'extras' => [
                            'icon' => 'plus'
                        ],
                    ])
                    ->setLabel(sprintf(
                        'lag_admin.%s.%s',
                        u($admin->getName())->lower()->snake()->toString(),
                        'create',
                    ))
                ;
            }
        }
        $this->dispatchMenuEvents('top', $menu);

        return $menu;
    }
}

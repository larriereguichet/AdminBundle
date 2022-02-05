<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event\Listener\Menu;

use LAG\AdminBundle\Admin\Resource\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Event\Events\Configuration\MenuConfigurationEvent;

class LeftMenuConfigurationListener
{
    private ResourceRegistryInterface $registry;

    public function __construct(ResourceRegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    public function __invoke(MenuConfigurationEvent $event): void
    {
        $menuConfiguration = $event->getMenuConfiguration();
        //$menuConfiguration['attributes']['class'] = 'navbar-nav bg-gradient-primary sidebar sidebar-dark accordion';
//        $menuConfiguration['attributes']['class'] = 'nav nav-pills flex-column mb-auto';
        $menuConfiguration['extras']['brand'] = true;
        $menuConfiguration['extras']['homepage'] = true;
        $menuConfiguration['children'] = $menuConfiguration['children'] ?? [];

        if (\count($menuConfiguration['children']) > 0) {
            foreach ($menuConfiguration['children'] as $index => $item) {
                if (empty($item['attributes']['class'])) {
                    //$item['attributes']['class'] = 'nav-item';
                    //$item['labelAttributes']['class'] = 'nav-link text-white';
                }

                if (empty($item['linkAttributes']['class'])) {
                    //$item['linkAttributes']['class'] = 'nav-link';
                }
                //$menuConfiguration['children'][$index] = $item;
            }
        } else {
            $menuConfiguration['children'] = [];

            // The default main menu is composed by links to the list action of each admin resource
//            foreach ($this->registry->all() as $resourceName => $resource) {
//                if (empty($resource->getConfiguration()['actions']) || !\array_key_exists('list', $resource->getConfiguration()['actions'])) {
//                    continue;
//                }
//                $menuConfiguration['children'][$resourceName] = [
//                    'admin' => $resourceName,
//                    'action' => 'list',
//                    'attributes' => [
//                        //'class' => 'nav-item',
//                    ],
//                    'linkAttributes' => [
//                        //'class' => 'nav-link',
//                    ],
//                ];
//            }
        }
        $event->setMenuConfiguration($menuConfiguration);
    }
}

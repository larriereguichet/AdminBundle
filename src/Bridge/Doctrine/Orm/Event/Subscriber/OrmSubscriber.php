<?php

namespace LAG\AdminBundle\Bridge\Doctrine\Orm\Event\Subscriber;

use LAG\AdminBundle\Event\AdminEvents;
use LAG\AdminBundle\Event\DoctrineOrmFilterEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OrmSubscriber implements EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            AdminEvents::DOCTRINE_ORM_FILTER => [
                ['addOrder'],
                ['addFilter'],
            ],
        ];
    }

    /**
     * Add the order to query builder according to the configuration.
     *
     * @param DoctrineOrmFilterEvent $event
     */
    public function addOrder(DoctrineOrmFilterEvent $event)
    {
        $queryBuilder = $event->getQueryBuilder();
        $admin = $event->getAdmin();
        $actionConfiguration = $admin->getAction()->getConfiguration();

        foreach ($actionConfiguration->getParameter('order') as $field => $order) {
            $alias = $queryBuilder->getRootAliases()[0];
            $queryBuilder->addOrderBy($alias.'.'.$field, $order);
        }
    }

    public function addFilter(DoctrineOrmFilterEvent $event)
    {
    }
}
<?php

namespace LAG\AdminBundle\Bridge\Doctrine\ORM\Event\Subscriber;

use LAG\AdminBundle\Event\AdminEvents;
use LAG\AdminBundle\Event\DoctrineOrmFilterEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ORMSubscriber implements EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            AdminEvents::DOCTRINE_ORM_FILTER => [
                ['addOrder'],
                ['addFilters'],
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

    /**
     * Add filter to the query builder.
     *
     * @param DoctrineOrmFilterEvent $event
     */
    public function addFilters(DoctrineOrmFilterEvent $event)
    {
        $queryBuilder = $event->getQueryBuilder();

        foreach ($event->getFilters() as $filter) {
            $alias = $queryBuilder->getRootAliases()[0];
            $parameterName = 'filter_'.$filter->getName();
            $value = $filter->getValue();

            if ('like' === $filter->getOperator()) {
                $value = '%'.$value.'%';
            }

            $queryBuilder->andWhere(sprintf(
                '%s.%s %s %s',
                $alias,
                $filter->getName(),
                $filter->getOperator(),
                ':'.$parameterName
            ));
            $queryBuilder->setParameter($parameterName, $value);
        }
    }
}

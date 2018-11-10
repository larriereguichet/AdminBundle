<?php

namespace LAG\AdminBundle\Bridge\Doctrine\ORM\Event\Subscriber;

use LAG\AdminBundle\Event\Events;
use LAG\AdminBundle\Event\DoctrineOrmFilterEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class ORMSubscriber implements EventSubscriberInterface
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * ORMSubscriber constructor.
     *
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::DOCTRINE_ORM_FILTER => [
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

        $request = $this->requestStack->getMasterRequest();
        $sort = $request->get('sort');
        $alias = $queryBuilder->getRootAliases()[0];

        // The sort from the request override the configured one
        if ($sort) {
            $order = $request->get('order', 'asc');

            $queryBuilder->addOrderBy($alias.'.'.$sort, $order);
        } else {
            foreach ($actionConfiguration->getParameter('order') as $field => $order) {

                $queryBuilder->addOrderBy($alias.'.'.$field, $order);
            }
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

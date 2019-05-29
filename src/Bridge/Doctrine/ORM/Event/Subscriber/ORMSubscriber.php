<?php

namespace LAG\AdminBundle\Bridge\Doctrine\ORM\Event\Subscriber;

use LAG\AdminBundle\Bridge\Doctrine\ORM\Metadata\MetadataHelperInterface;
use LAG\AdminBundle\Event\Events;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Event\ORMFilterEvent;
use LAG\AdminBundle\Event\Events\FieldEvent;
use LAG\AdminBundle\Event\Events\FormEvent;
use LAG\AdminBundle\Field\Definition\FieldDefinition;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class ORMSubscriber implements EventSubscriberInterface
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var FieldDefinition[]
     */
    private $fieldDefinitions;

    /**
     * @var MetadataHelperInterface
     */
    private $metadataHelper;

    public function __construct(RequestStack $requestStack, MetadataHelperInterface $metadataHelper)
    {
        $this->requestStack = $requestStack;
        $this->metadataHelper = $metadataHelper;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            Events::DOCTRINE_ORM_FILTER => [
                ['addOrder'],
                ['addFilters'],
            ],
            Events::FIELD_PRE_CREATE => [
                ['guessType'],
            ],
            Events::FORM_PRE_CREATE_ENTITY_FORM => [
                ['guessFormType'],
            ],
        ];
    }

    /**
     * Add the order to query builder according to the configuration.
     *
     * @param ORMFilterEvent $event
     */
    public function addOrder(ORMFilterEvent $event)
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
     * @param ORMFilterEvent $event
     */
    public function addFilters(ORMFilterEvent $event)
    {
        $queryBuilder = $event->getQueryBuilder();

        foreach ($event->getFilters() as $filter) {
            $alias = $queryBuilder->getRootAliases()[0];
            $parameterName = 'filter_'.$filter->getName();
            $value = $filter->getValue();

            if ('like' === $filter->getComparator()) {
                $value = '%'.$value.'%';
            }

            if ('and' === $filter->getOperator()) {
                $method = 'andWhere';
            } else {
                $method = 'orWhere';
            }

            $queryBuilder->$method(sprintf(
                '%s.%s %s %s',
                $alias,
                $filter->getName(),
                $filter->getComparator(),
                ':'.$parameterName
            ));
            $queryBuilder->setParameter($parameterName, $value);
        }
    }

    public function guessType(FieldEvent $event): void
    {
        // No need to guess a type when one is already defined
        if (null !== $event->getType()) {
            return;
        }

        // Initialize the definitions array only the first time
        if (null === $this->fieldDefinitions) {
            $this->fieldDefinitions = $this->metadataHelper->getFields($event->getEntityClass());
        }

        // By default, ids should be displayed as a string
        if ('id' === $event->getFieldName()) {
            $event->setType('string');

            return;
        }

        // No need to guess a type when the field is not defined
        if (!key_exists($event->getFieldName(), $this->fieldDefinitions)) {
            return;
        }
        $definition = $this->fieldDefinitions[$event->getFieldName()];
        $event->setType($definition->getType());
        $event->setOptions($definition->getOptions());
    }

    public function guessFormType(FormEvent $event): void
    {
        $configuration = $event->getAdmin()->getConfiguration();

        // No need to guess a type when one is already defined
        if (null !== $configuration->get('form')) {
            return;
        }

        // Initialize the definitions array only the first time
        if (null === $this->fieldDefinitions) {
            $this->fieldDefinitions = $this->metadataHelper->getFields($configuration->get('entity'));
        }

        foreach ($this->fieldDefinitions as $name => $definition) {
            $event->addFieldDefinition($name, $definition);
        }
    }
}

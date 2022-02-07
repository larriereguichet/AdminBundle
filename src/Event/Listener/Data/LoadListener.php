<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event\Listener\Data;

use LAG\AdminBundle\Admin\AdminAwareInterface;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\DataProvider\DataSourceHandler\DataHandlerInterface;
use LAG\AdminBundle\DataProvider\Registry\DataProviderRegistryInterface;
use LAG\AdminBundle\Event\AdminEvents;
use LAG\AdminBundle\Event\Events\DataEvent;
use LAG\AdminBundle\Event\Events\DataFilterEvent;
use LAG\AdminBundle\Event\Events\DataOrderEvent;
use LAG\AdminBundle\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Load data from the database, according ot the configured strategy on the current action.
 */
class LoadListener
{
    public function __construct(
        private DataProviderRegistryInterface $registry,
        private DataHandlerInterface $dataHandler,
        private EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function __invoke(DataEvent $event): void
    {
        $request = $event->getRequest();
        $admin = $event->getAdmin();

        // The load strategy is used to know how many entities should be load in the current action. It should be define
        // in the action instead of the admin as each action usually have a different load strategy
        $actionConfiguration = $admin->getAction()->getConfiguration();
        $strategy = $actionConfiguration->getLoadStrategy();

        // If another event already load data, do not load twice
        if ($event->getData() !== null) {
            return;
        }
        // The data provider is configured in the admin as it make no sense to have different data provider for each
        // action in the same admin. It should be a service tagged with a name
        $dataProviderName = $admin->getConfiguration()->getDataProvider();
        $dataProvider = $this->registry->get($dataProviderName);

        if ($dataProvider instanceof AdminAwareInterface) {
            $dataProvider->setAdmin($admin);
        }

        if ($strategy === AdminInterface::LOAD_STRATEGY_NONE) {
            $data = $dataProvider->create($admin->getEntityClass());
            $event->setData($data);
        }

        // Load only one entity from the database using its identifier (for the edit action for example)
        if ($strategy === AdminInterface::LOAD_STRATEGY_UNIQUE) {
            // Only single identifier are handled yet (no composite key)
            $identifier = $this->extractIdentifier($request, $actionConfiguration->getRouteParameters());
            $data = $dataProvider->get($admin->getEntityClass(), $identifier);
            $event->setData($data);
        }

        // Load several entities from the database (for the list action for example)
        if ($strategy === AdminInterface::LOAD_STRATEGY_MULTIPLE) {
            // Filters are provided in the FilterDataListener using the value from the configuration and the submitted
            // filters form. SO they should be taken from the event only
            $filters = $event->getFilters();

            // In the same way, if order is provided via an event (when clicking on a sortable column for instance) it
            // should override the default order configured in action
            $orderBy = $event->getOrderBy();

            if (\count($orderBy) === 0) {
                $orderBy = $actionConfiguration->getOrder();
            }
            $page = $request->get($actionConfiguration->getPageParameter(), 1);

            // Create a data source according to the data provider
            $data = $dataProvider->getCollection(
                $admin->getEntityClass(),
                $filters,
                $orderBy,
                $page,
                $actionConfiguration->getMaxPerPage()
            );
            // Dynamic filtering and ordering
            $this->eventDispatcher->dispatch(new DataFilterEvent($admin, $data, $filters), AdminEvents::ADMIN_FILTER);
            $this->eventDispatcher->dispatch(new DataOrderEvent($admin, $data, $orderBy), AdminEvents::ADMIN_ORDER);

            // Transform results into displayable data (a query builder into a pager for instance)
            $data = $this->dataHandler->handle($data);
            $event->setData($data);
        }
    }

    private function extractIdentifier(Request $request, array $routeRequirements): mixed
    {
        foreach ($routeRequirements as $name => $requirement) {
            if ($request->get($name) !== null) {
                return $request->get($name);
            }
        }

        throw new Exception('Unable to find a identifier in the request for the load strategy "unique"');
    }
}

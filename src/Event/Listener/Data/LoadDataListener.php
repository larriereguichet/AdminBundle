<?php

namespace LAG\AdminBundle\Event\Listener\Data;

use LAG\AdminBundle\Admin\AdminAwareInterface;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\DataProvider\Registry\DataProviderRegistryInterface;
use LAG\AdminBundle\Event\Events\DataEvent;
use LAG\AdminBundle\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;

/**
 * Load data from the database, according ot the configured strategy on the current action.
 */
class LoadDataListener
{
    private DataProviderRegistryInterface $registry;

    public function __construct(DataProviderRegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    public function __invoke(DataEvent $event): void
    {
        $request = $event->getRequest();
        $admin = $event->getAdmin();

        // The load strategy is used to know how many entities should be load in the current action. It should be define
        // in the action instead of the admin as each action usually have a different load strategy
        $actionConfiguration = $admin->getAction()->getConfiguration();
        $strategy = $actionConfiguration->getLoadStrategy();

        // Some action (as create for example) does not require to load data from the database
        if ($strategy === AdminInterface::LOAD_STRATEGY_NONE) {
            return;
        }
        // The data provider is configured in the admin as it make no sense to hae different data provider for each
        // action in the same admin. It should be a service tagged with a name
        $dataProviderName = $admin->getConfiguration()->getDataProvider();
        $dataProvider = $this->registry->get($dataProviderName);

        if ($dataProvider instanceof AdminAwareInterface) {
            $dataProvider->setAdmin($admin);
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
            $orderBy = array_merge($actionConfiguration->getOrder(), $event->getOrderBy());

            $page = $request->get($actionConfiguration->getPageParameter(), 1);
            $data = $dataProvider->getCollection(
                $admin->getEntityClass(),
                $filters,
                $orderBy,
                $page,
                $actionConfiguration->getMaxPerPage()
            );
            $event->setData($data);
        }
    }

    private function extractIdentifier(Request $request, array $routeRequirements)
    {
        $identifier = null;

        foreach ($routeRequirements as $name => $requirement) {
            if ($request->get($name) !== null) {
                return $request->get($name);
            }
        }

        throw new Exception('Unable to find a identifier in the request for the load strategy "unique"');
    }
}

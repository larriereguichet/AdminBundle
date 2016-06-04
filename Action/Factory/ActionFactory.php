<?php

namespace LAG\AdminBundle\Action\Factory;

use LAG\AdminBundle\Action\Action;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Admin\Factory\FilterFactory;
use LAG\AdminBundle\Configuration\Factory\ConfigurationFactory;
use LAG\AdminBundle\Field\Factory\FieldFactory;

class ActionFactory
{
    /**
     * @var FieldFactory
     */
    protected $fieldFactory;

    /**
     * @var FilterFactory
     */
    protected $filterFactory;

    /**
     * @var ConfigurationFactory
     */
    protected $configurationFactory;

    /**
     * ActionFactory constructor.
     *
     * @param FieldFactory $fieldFactory
     * @param FilterFactory $filterFactory
     * @param ConfigurationFactory $configurationFactory
     */
    public function __construct(
        FieldFactory $fieldFactory,
        FilterFactory $filterFactory,
        ConfigurationFactory $configurationFactory
    ) {
        $this->fieldFactory = $fieldFactory;
        $this->filterFactory = $filterFactory;
        $this->configurationFactory = $configurationFactory;
    }

    /**
     * Create an Action from configuration values.
     *
     * @param string $actionName
     * @param array $configuration
     * @param AdminInterface $admin
     *
     * @return Action
     */
    public function create($actionName, array $configuration, AdminInterface $admin)
    {
        // create action configuration object
        $actionConfiguration = $this
            ->configurationFactory
            ->createActionConfiguration($actionName, $admin, $configuration);

        // create action
        $action = new Action($actionName, $actionConfiguration);

        // adding fields items to actions
        foreach ($actionConfiguration->getParameter('fields') as $fieldName => $fieldConfiguration) {
            $field = $this
                ->fieldFactory
                ->create($fieldName, $fieldConfiguration);
            $action->addField($field);
        }

        // adding filters to the action
        foreach ($actionConfiguration->getParameter('filters') as $fieldName => $filterConfiguration) {
            $filter = $this
                ->filterFactory
                ->create($fieldName, $filterConfiguration);
            $action->addFilter($filter);
        }

        return $action;
    }
}

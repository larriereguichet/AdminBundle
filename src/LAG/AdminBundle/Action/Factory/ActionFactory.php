<?php

namespace LAG\AdminBundle\Action\Factory;

use Exception;
use LAG\AdminBundle\Action\Action;
use LAG\AdminBundle\Action\ActionInterface;
use LAG\AdminBundle\Action\Event\ActionCreatedEvent;
use LAG\AdminBundle\Action\Event\ActionEvents;
use LAG\AdminBundle\Action\Registry\Registry;
use LAG\AdminBundle\Admin\Configuration\AdminConfiguration;
use LAG\AdminBundle\DataProvider\Factory\DataProviderFactory;
use LAG\AdminBundle\DataProvider\Loader\EntityLoader;
use LAG\AdminBundle\DataProvider\Loader\PaginatedEntityLoader;
use LAG\AdminBundle\Field\Factory\FieldFactory;
use LAG\AdminBundle\Filter\Factory\FilterFormBuilder;
use LAG\AdminBundle\LAGAdminBundle;
use LogicException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * This class allow to inject an Admin into a Controller.
 */
class ActionFactory
{
    /**
     * @var ConfigurationFactory
     */
    private $configurationFactory;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;
    
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var FieldFactory
     */
    private $fieldFactory;

    /**
     * @var FilterFormBuilder
     */
    private $filterFormBuilder;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;
    
    /**
     * @var DataProviderFactory
     */
    private $dataProviderFactory;

    /**
     * ActionFactory constructor.
     *
     * @param ConfigurationFactory     $configurationFactory
     * @param EventDispatcherInterface $eventDispatcher
     * @param FieldFactory             $fieldFactory
     * @param FilterFormBuilder        $filterFormBuilder
     * @param FormFactoryInterface     $formFactory
     * @param DataProviderFactory      $dataProviderFactory
     */
    public function __construct(
        ConfigurationFactory $configurationFactory,
        EventDispatcherInterface $eventDispatcher,
        FieldFactory $fieldFactory,
        FilterFormBuilder $filterFormBuilder,
        FormFactoryInterface $formFactory,
        DataProviderFactory $dataProviderFactory
    ) {
        $this->configurationFactory = $configurationFactory;
        $this->eventDispatcher = $eventDispatcher;
        $this->fieldFactory = $fieldFactory;
        $this->filterFormBuilder = $filterFormBuilder;
        $this->formFactory = $formFactory;
        $this->dataProviderFactory = $dataProviderFactory;
    }

    /**
     * @param string             $name
     * @param array              $configuration
     * @param string             $adminName
     * @param AdminConfiguration $adminConfiguration
     *
     * @return Action
     *
     * @throws Exception
     */
    public function create($name, array $configuration, $adminName, AdminConfiguration $adminConfiguration)
    {
        $configuration = $this
            ->configurationFactory
            ->create($name, $adminName, $adminConfiguration, $configuration)
        ;
        $fields = [];

        foreach ($configuration->getParameter('fields') as $fieldName => $fieldConfiguration) {
            $fields[$name] = $this
                ->fieldFactory
                ->create($fieldName, $fieldConfiguration, $configuration)
            ;
        }

        // Retrieve a data provider and load it into the entity loader
        $dataProvider = $this
            ->dataProviderFactory
            ->get($configuration->getParameter('data_provider'), $configuration->getParameter('entity'))
        ;
        
        if ($configuration->getParameter('pager')) {
            $entityLoader = new PaginatedEntityLoader($dataProvider);
        } else {
            $entityLoader = new EntityLoader($dataProvider);
        }

        // Build the configured forms
        $forms = [];
        $filterForm = $this
            ->filterFormBuilder
            ->build($configuration)
        ;

        if (null !== $filterForm) {
            $forms['filter_form'] = $filterForm;
        }

        if (null !== $configuration->getParameter('form')) {
            $forms['form'] = $this
                ->formFactory
                ->create($configuration->getParameter('form'), $configuration->getParameter('form_options'))
            ;
        }

        $action = new Action(
            $name,
            $configuration,
            $entityLoader,
            $fields,
            $forms
        );

        // Allow users to listen after action creation
        $event = new ActionCreatedEvent($action, $adminName, $adminConfiguration);
        $this
            ->eventDispatcher
            ->dispatch(
                ActionEvents::ACTION_CREATED, $event)
        ;

        return $action;
    }
    
    /**
     * Return the Actions of an Admin configuration. Each Action will be retrieved from the Action registry. The key
     * will be retrieved either in the service configuration key if provided, either from the default service mapping
     * configuration.
     *
     * @param string $adminName
     * @param array  $configuration
     *
     * @return ActionInterface[]
     *
     * @throws Exception
     */
    public function getActions($adminName, array $configuration)
    {
        $actions = [];
    
        if (!key_exists('actions', $configuration)) {
            throw new Exception('Invalid configuration for admin "'.$adminName.'"');
        }
        
        foreach ($configuration['actions'] as $name => $actionConfiguration) {
            
            if (null !== $actionConfiguration && key_exists('service', $actionConfiguration)) {
                // if a service key is defined, take it
                $serviceId = $actionConfiguration['service'];
            } else {
                // if no service key was provided, we take the default action service
                $serviceId = $this->getDefaultActionServiceId($name, $adminName);
            }
            $action = $this
                ->registry
                ->get($serviceId)
            ;
            $actions[$name] = $action;
        }
        
        return $actions;
    }
}

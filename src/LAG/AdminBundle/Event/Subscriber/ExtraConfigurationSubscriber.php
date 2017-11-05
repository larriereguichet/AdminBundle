<?php

namespace LAG\AdminBundle\Event\Subscriber;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use LAG\AdminBundle\Action\Event\ActionEvents;
use LAG\AdminBundle\Action\Event\BeforeConfigurationEvent;
use LAG\AdminBundle\Admin\Behaviors\TranslationKeyTrait;
use LAG\AdminBundle\Admin\Event\AdminCreateEvent;
use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Admin\Event\AdminEvents;
use LAG\AdminBundle\Application\Configuration\ApplicationConfigurationStorage;
use LAG\AdminBundle\Routing\RouteNameGenerator;
use LAG\AdminBundle\Utils\FieldTypeGuesser;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Add extra default configuration for actions and fields. Bind to ADMIN_CREATE and ACTION_CREATE events
 */
class ExtraConfigurationSubscriber implements EventSubscriberInterface
{
    use TranslationKeyTrait;

    /**
     * If is true, the extra configuration will be added.
     *
     * @var boolean
     */
    protected $extraConfigurationEnabled = false;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var ApplicationConfiguration
     */
    protected $applicationConfiguration;

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            AdminEvents::ADMIN_CREATE => 'adminCreate',
            ActionEvents::BEFORE_CONFIGURATION => 'beforeActionConfiguration',
        ];
    }
    
    /**
     * ExtraConfigurationSubscriber constructor.
     *
     * @param boolean                         $extraConfigurationEnabled
     * @param Registry                        $doctrine
     * @param ApplicationConfigurationStorage $applicationConfigurationStorage
     */
    public function __construct(
        $extraConfigurationEnabled = true,
        Registry $doctrine,
        ApplicationConfigurationStorage $applicationConfigurationStorage
    ) {
        $this->extraConfigurationEnabled = $extraConfigurationEnabled;
        // entity manager can be closed, so its better to inject the Doctrine registry instead
        $this->entityManager = $doctrine->getManager();
        $this->applicationConfiguration = $applicationConfigurationStorage->getApplicationConfiguration();
    }

    /**
     * Adding default CRUD if none is defined.
     *
     * @param AdminCreateEvent $event
     */
    public function adminCreate(AdminCreateEvent $event)
    {
        if (!$this->extraConfigurationEnabled) {
            return;
        }
        $configuration = $event->getAdminConfiguration();

        // if no actions are defined, we set default CRUD action
        if (!array_key_exists('actions', $configuration) || !count($configuration['actions'])) {
            $configuration['actions'] = [
                'create' => [],
                'list' => [],
                'edit' => [],
                'delete' => [],
            ];
            $event->setAdminConfiguration($configuration);
        }
    }

    /**
     * Add default linked actions and default menu actions.
     *
     * @param BeforeConfigurationEvent $event
     */
    public function beforeActionConfiguration(BeforeConfigurationEvent $event)
    {
        // add configuration only if extra configuration is enabled
        if (!$this->extraConfigurationEnabled) {
            return;
        }
        // Action configuration array
        $configuration = $event->getActionConfiguration();
        
        // current Admin
        $admin = $event->getAdmin();
        
        // allowed Actions according to the admin
        $keys = $admin
            ->getConfiguration()
            ->getParameter('actions');
        $allowedActions = array_keys($keys);

        // add default menu configuration
        $configuration = $this->addDefaultMenuConfiguration(
            $admin->getName(),
            $event->getActionName(),
            $configuration,
            $allowedActions
        );

        // guess field configuration if required
        $this->guessFieldConfiguration(
            $configuration,
            $event->getActionName(),
            $admin->getConfiguration()->getParameter('entity')
        );

        // guess linked actions for list actions
        $this->guessLinkedActionsConfiguration(
            $configuration,
            $allowedActions,
            $event
        );

        // reset action configuration
        $event->setActionConfiguration($configuration);
    }

    /**
     * Add default menu configuration for an action.
     *
     * @param string $adminName
     * @param string $actionName
     * @param array $actionConfiguration
     * @param array $allowedActions
     * @return array The modified configuration
     */
    protected function addDefaultMenuConfiguration(
        $adminName,
        $actionName,
        array $actionConfiguration,
        array $allowedActions
    ) {
        // we add a default top menu item "create" only for list action
        if ($actionName === 'list') {

            // the create action should enabled
            if (in_array('create', $allowedActions)) {

                if (!array_key_exists('menus', $actionConfiguration)) {
                    $actionConfiguration['menus'] = [];
                }
                // if the menu is disabled we do not add the menu item
                if ($actionConfiguration['menus'] !== false) {
                    $resolver = new OptionsResolver();
                    $resolver->setDefaults([
                        'top' => [
                            'items' => [
                                'create' => [
                                    'admin' => $adminName,
                                    'action' => 'create',
                                    'icon' => 'fa fa-plus',
                                ]
                            ]
                        ]
                    ]);
                    // resolve default menu options
                    $actionConfiguration['menus'] = $resolver->resolve($actionConfiguration['menus']);
                }
            }
        }

        return $actionConfiguration;
    }

    /**
     * If no field was provided, try to guess field
     *
     * @param array $configuration
     * @param $actionName
     * @param $entityClass
     */
    protected function guessFieldConfiguration(array &$configuration, $actionName, $entityClass)
    {
        // if no field was provided in configuration, we try to take fields from doctrine metadata
        if (empty($configuration['fields']) || !count($configuration['fields'])) {
            $fields = [];
            $guesser = new FieldTypeGuesser();
            $metadata = $this
                ->entityManager
                ->getMetadataFactory()
                ->getMetadataFor($entityClass);
            $fieldsName = $metadata->getFieldNames();

            foreach ($fieldsName as $name) {
                $type = $metadata->getTypeOfField($name);
                // get field type from doctrine type
                $fieldConfiguration = $guesser->getTypeAndOptions($type);

                // if a field configuration was found, we take it
                if (count($fieldConfiguration)) {
                    $fields[$name] = $fieldConfiguration;
                }
            }
            if (count($fields)) {
                // adding new fields to action configuration
                $configuration['fields'] = $fields;

                if ($actionName == 'list') {
                    $configuration['fields']['_actions'] = null;
                }
            }
        }
    }

    /**
     * Add fake link fields to edit and delete action for the list view.
     *
     * @param array $configuration
     * @param array $allowedActions
     * @param BeforeConfigurationEvent $event
     */
    protected function guessLinkedActionsConfiguration(
        array &$configuration,
        array $allowedActions,
        BeforeConfigurationEvent $event
    ) {
        if (!array_key_exists('fields', $configuration)) {
            return;
        }
        // configured linked actions :
        // _action key should exists and be null
        $_actionExists = array_key_exists('_actions', $configuration['fields']);
        $_actionIsNull = $_actionExists && $configuration['fields']['_actions'] === null;

        // _action is added extra configuration only for the "list" action
        $isListAction = $event->getActionName() == 'list';

        if ($_actionExists && $_actionIsNull && $isListAction) {
            // in list view, we add by default and an edit and a delete button
            $translationPattern = $this
                ->applicationConfiguration
                ->getParameter('translation')['pattern'];
            
            // add a link to the "delete" action, if it is allowed
            if (in_array('delete', $allowedActions)) {
                $generator = new RouteNameGenerator();
                $admin = $event->getAdmin();
                
                $configuration['fields']['_actions'] = [
                    'type'=> 'action',
                    'options' => [
                        'title' => $this->getTranslationKey($translationPattern, 'delete', $admin->getName()),
                        'route' => $generator->generate('delete', $admin->getName(), $admin->getConfiguration()),
                        'parameters' => [
                            'id' => false
                        ],
                        'icon' => 'remove'
                    ]
                ];
            }
        }
    }
}

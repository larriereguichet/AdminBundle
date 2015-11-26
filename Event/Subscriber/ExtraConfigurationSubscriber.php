<?php

namespace LAG\AdminBundle\Event\Subscriber;

use Doctrine\Common\Persistence\Mapping\MappingException;
use Doctrine\ORM\EntityManager;
use LAG\AdminBundle\Admin\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Event\AdminEvent;
use LAG\AdminBundle\Utils\FieldTypeGuesser;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Add extra default configuration for actions and fields. Bind to ADMIN_CREATE and ACTION_CREATE events
 */
class ExtraConfigurationSubscriber implements EventSubscriberInterface
{
    /**
     * @var bool
     */
    protected $enableExtraConfiguration;

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
            AdminEvent::ADMIN_CREATE => 'adminCreate',
            AdminEvent::ACTION_CREATE => 'actionCreate',
        ];
    }

    /**
     * ExtraConfigurationSubscriber constructor
     *
     * @param bool|true $enableExtraConfiguration
     * @param EntityManager $entityManager
     * @param ApplicationConfiguration $applicationConfiguration
     */
    public function __construct($enableExtraConfiguration = true, EntityManager $entityManager, ApplicationConfiguration $applicationConfiguration)
    {
        $this->enableExtraConfiguration = $enableExtraConfiguration;
        $this->entityManager = $entityManager;
        $this->applicationConfiguration = $applicationConfiguration;
    }

    /**
     * Adding default CRUD if none is defined
     *
     * @param AdminEvent $event
     */
    public function adminCreate(AdminEvent $event)
    {
        if (!$this->enableExtraConfiguration) {
            return;
        }
        $configuration = $event->getConfiguration();

        // if no actions are defined, we set default CRUD action
        if (!array_key_exists('actions', $configuration) || !count($configuration['actions'])) {
            $configuration['actions'] = [
                'create' => [],
                'list' => [],
                'edit' => [],
                'delete' => [],
                'batch' => []
            ];
        } else {
            $actions = $configuration['actions'];

            foreach ($actions as $name => $action) {
                if (!array_key_exists('batch', $action) || !count($action['batch'])) {
                    if ($name == 'list') {
                        $configuration['actions'][$name]['batch'] = [
                            'delete'
                        ];
                    }
                }
            }
        }
        $event->setConfiguration($configuration);
    }

    /**
     * Add default linked actions and default menu actions
     *
     * @param AdminEvent $event
     * @throws MappingException
     */
    public function actionCreate(AdminEvent $event)
    {
        // add configuration only if extra configuration is enabled
        if (!$this->enableExtraConfiguration) {
            return;
        }
        // action configuration array
        $configuration = $event->getConfiguration();
        // current action admin
        $admin = $event->getAdmin();
        // allowed actions according to the admin
        $keys = $admin
            ->getConfiguration()
            ->getActions();
        $allowedActions = array_keys($keys);

        // if no field was provided in configuration, we try to take fields from doctrine metadata
        if (empty($configuration['fields']) || !count($configuration['fields'])) {
            $fields = [];
            $guesser = new FieldTypeGuesser();
            $metadata = $this
                ->entityManager
                ->getMetadataFactory()
                ->getMetadataFor($admin->getEntityNamespace());
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
            }
        }
        // configured linked actions
        if (array_key_exists('_actions', $configuration['fields'])
            && !array_key_exists('type', $configuration['fields']['_actions'])
        ) {
            // in list view, we add by default and an edit and a delete button
            if ($event->getActionName() == 'list') {
                if (in_array('edit', $allowedActions)) {
                    $configuration['fields']['_actions']['type'] = 'collection';
                    $configuration['fields']['_actions']['options']['_edit'] = [
                        'type' => 'action',
                        'options' => [
                            'title' => $this->applicationConfiguration->getTranslationKey('edit', $event->getAdmin()->getName()),
                            'route' => $admin->generateRouteName('edit'),
                            'parameters' => [
                                'id' => false
                            ],
                            'icon' => 'pencil'
                        ]
                    ];
                }
                if (in_array('delete', $allowedActions)) {
                    $configuration['fields']['_actions']['type'] = 'collection';
                    $configuration['fields']['_actions']['options']['_delete'] = [
                        'type' => 'action',
                        'options' => [
                            'title' => $this->applicationConfiguration->getTranslationKey('delete', $event->getAdmin()->getName()),
                            'route' => $admin->generateRouteName('delete'),
                            'parameters' => [
                                'id' => false
                            ],
                            'icon' => 'remove'
                        ]
                    ];
                }
            }
        }
        // add default menu actions if none was provided
        if (empty($configuration['actions'])) {
            // by default, in list action we add a create linked action
            if ($event->getActionName() == 'list') {
                if (in_array('create', $allowedActions)) {
                    $configuration['actions']['create'] = [
                        'title' => $this->applicationConfiguration->getTranslationKey('create', $event->getAdmin()->getName()),
                        'route' => $admin->generateRouteName('create'),
                        'icon' => 'pencil'
                    ];
                }
            }
        }
        if (empty($configuration['batch'])) {
            if ($event->getActionName() == 'list') {
                $configuration['batch'] = [
                    'delete'
                ];
            }
        }
        // reset action configuration
        $event->setConfiguration($configuration);
    }
}

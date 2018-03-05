<?php

namespace LAG\AdminBundle\Event\Subscriber;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Configuration\ApplicationConfigurationStorage;
use LAG\AdminBundle\Event\AdminEvents;
use LAG\AdminBundle\Event\ConfigurationEvent;
use LAG\AdminBundle\LAGAdminBundle;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Add extra default configuration for actions and fields.
 */
class ExtraConfigurationSubscriber implements EventSubscriberInterface
{
    /**
     * @var ApplicationConfiguration
     */
    private $applicationConfiguration;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            AdminEvents::ACTION_CONFIGURATION => 'actionConfiguration',
        ];
    }

    /**
     * ExtraConfigurationSubscriber constructor.
     *
     * @param ApplicationConfigurationStorage $applicationConfigurationStorage
     * @param EntityManagerInterface          $entityManager
     */
    public function __construct(
        ApplicationConfigurationStorage $applicationConfigurationStorage,
        EntityManagerInterface $entityManager
    )
    {
        $this->applicationConfiguration = $applicationConfigurationStorage->getConfiguration();
        $this->entityManager = $entityManager;
    }

    public function actionConfiguration(ConfigurationEvent $event)
    {
        if (!$this->applicationConfiguration->getParameter('enable_extra_configuration')) {
            return;
        }
        $configuration = $event->getConfiguration();

        $this->addDefaultActions($configuration);
        $this->addDefaultTopMenu($configuration, $event->getAdminName());
        $this->addDefaultFields($configuration, $event->getEntityClass(), $event->getAdminName());
        $this->addDefaultStrategy($configuration);

        $event->setConfiguration($configuration);
    }

    /**
     * Defines the default CRUD actions if no action was configured.
     *
     * @param array $configuration
     */
    private function addDefaultActions(array &$configuration)
    {
        if (!key_exists('actions', $configuration) || !is_array($configuration['actions'])) {
            $configuration['actions'] = [];
        }

        if (0 !== count($configuration['actions'])) {
            return;
        }
        $configuration['actions'] = [
            'create' => [],
            'list' => [],
            'edit' => [],
            'delete' => [],
        ];
    }

    private function addDefaultTopMenu(array &$configuration, $adminName)
    {
        if (!key_exists('list', $configuration)) {
            return;
        }
        if (!key_exists('create', $configuration)) {
            return;
        }

        if (!key_exists('menus', $configuration['list'])) {
            $configuration['list']['menus'] = [];
        }

        // If the menu is set to false, menus are disabled for this action
        if (false !== $configuration['list']['menus']) {
            return;
        }

        $configuration['list']['menus']['top'] = [
            'items' => [
                'create' => [
                    'admin' => $adminName,
                    'action' => 'create',
                    'icon' => 'fa fa-plus',
                ],
            ],
        ];
    }

    private function addDefaultFields(array &$configuration, $entityClass, $adminName)
    {
        $fieldsMapping = [
            'string' => [
                'type' => 'string',
                'options' => [
                    'length' => 100,
                ],
            ],
            'boolean' => [
                'type' => 'boolean',
                'options' => [],
            ],
            'datetime' => [
                'type' => 'date',
                'options' => [],
            ],
        ];

        foreach ($configuration['actions'] as $actionName => $action) {
            if (null === $action) {
                $action = [];
            }
            $metadata = null;

            try {
                // We could not use the hasMetadataFor() method as it is not working if the entity is not loaded. But
                // the getMetadataFor() method could throw an exception if the class is not found
                $metadata = $this->entityManager->getMetadataFactory()->getMetadataFor($entityClass);
            } catch (Exception $exception) {}

            // If fields are already defined, nothing to do
            if (key_exists('fields', $action) && is_array($action['fields']) && count($action['fields'])) {
                $fields = $action['fields'];
            } else {
                $fields = [];

                // Get fields names from the metadata if no configuration is defined
                foreach ($metadata->getFieldNames() as $fieldName) {
                    $fields[$fieldName] = null;
                }
            }
            $actionField = $this->getActionField($metadata->getFieldNames());

            foreach ($fields as $fieldName => $fieldConfiguration) {
                $fieldType = $metadata->getTypeOfField($fieldName);

                if (
                    'list' === $actionName &&
                    $fieldName === $actionField &&
                    key_exists('edit', $configuration['actions'])
                ) {
                    $fieldConfiguration = [
                        'type' => 'action',
                        'options' => [
                            'admin' => $adminName,
                            'action' => 'edit',
                            'parameters' => [
                                'id' => null,
                            ],
                        ]
                    ];

                } else if (
                    '_delete' === $fieldName &&
                    !$metadata->hasField('_delete') &&
                    null === $fieldConfiguration &&
                    key_exists('delete', $configuration['actions'])
                ) {
                    // If a "delete" field is declared, and if it is not configured in the metadata, and if no
                    // configuration is declared for this field, and if the "delete" action is allowed, we add a default
                    // "button" configuration
                    $fieldConfiguration = [
                        'type' => 'link',
                        'options' => [
                            'admin' => $adminName,
                            'action' => 'delete',
                            'parameters' => [
                                'id' => null,
                            ],
                            'text' => 'test',
                            'class' => 'btn btn-sm btn-danger',
                            'icon' => 'remove',
                        ],
                    ];

                } else if (key_exists($fieldType, $fieldsMapping)) {
                    $fieldConfiguration = $fieldsMapping[$metadata->getTypeOfField($fieldName)];
                }
                $configuration['actions'][$actionName]['fields'][$fieldName] = $fieldConfiguration;
            }
        }
    }

    private function addDefaultStrategy(array &$configuration)
    {
        $mapping = [
            'list' => LAGAdminBundle::LOAD_STRATEGY_MULTIPLE,
            'create' => LAGAdminBundle::LOAD_STRATEGY_NONE,
            'delete' => LAGAdminBundle::LOAD_STRATEGY_UNIQUE,
            'edit' => LAGAdminBundle::LOAD_STRATEGY_UNIQUE,
        ];

        foreach ($configuration['actions'] as $name => $action) {
            if (null === $action) {
                continue;
            }

            if (key_exists('load_strategy', $action)) {
                continue;
            }

            if (!key_exists($name, $mapping)) {
                continue;
            }
            $configuration['actions'][$name]['load_strategy'] = $mapping[$name];
        }
    }

    private function getActionField(array $fields)
    {
        $mapping = [
            'title',
            'name',
            'id',
        ];

        foreach ($mapping as $name) {
            if (in_array($name, $fields)) {
                return $name;
            }
        }

        return null;
    }
}

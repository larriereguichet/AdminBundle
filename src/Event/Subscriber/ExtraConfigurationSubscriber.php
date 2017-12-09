<?php

namespace LAG\AdminBundle\Event\Subscriber;

use Doctrine\ORM\EntityManagerInterface;
use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Configuration\ApplicationConfigurationStorage;
use LAG\AdminBundle\Event\AdminEvents;
use LAG\AdminBundle\Event\ConfigurationEvent;
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
        $this->addDefaultFields($configuration, $event->getEntityClass());

        $event->setConfiguration($configuration);
    }

    /**
     * Defines the default CRUD actions if no action was configured.
     *
     * @param array $configuration
     */
    private function addDefaultActions(array &$configuration)
    {
        if (!array_key_exists('actions', $configuration) || is_array($configuration['actions'])) {
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
        if (!array_key_exists('list', $configuration)) {
            return;
        }
        if (!array_key_exists('create', $configuration)) {
            return;
        }

        if (!array_key_exists('menus', $configuration['list'])) {
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

    private function addDefaultFields(array &$configuration, $entityClass)
    {
        $fieldMapping = [
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
            // If fields are already defined, nothing to do
            if (array_key_exists('fields', $action) && is_array($action['fields']) && count($action['fields'])) {
                continue;
            }

            if (!$this->entityManager->getMetadataFactory()->hasMetadataFor($entityClass)) {
                continue;
            }
            $metadata = $this
                ->entityManager
                ->getMetadataFactory()
                ->getMetadataFor($entityClass)
            ;

            foreach ($metadata->getFieldNames() as $fieldName) {
                $fieldConfiguration = [];

                if (!array_key_exists($metadata->getTypeOfField($fieldName), $fieldMapping)) {
                    $fieldConfiguration = $fieldMapping[$metadata->getTypeOfField($fieldName)];
                }
                $configuration['actions'][$actionName]['fields'][$fieldName] = $fieldConfiguration;
            }
        }
    }
}

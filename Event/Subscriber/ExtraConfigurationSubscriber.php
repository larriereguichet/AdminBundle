<?php

namespace LAG\AdminBundle\Event\Subscriber;

use Doctrine\ORM\EntityManager;
use LAG\AdminBundle\Admin\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Event\AdminEvent;
use LAG\AdminBundle\Utils\FieldTypeGuesser;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

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

    public static function getSubscribedEvents()
    {
        return [
            AdminEvent::ADMIN_CREATE => 'adminCreate',
            AdminEvent::ACTION_CREATE => 'actionCreate',
        ];
    }

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
                'delete' => []
            ];
        }
        $event->setConfiguration($configuration);
    }

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
        // add default menu actions if none was provided
        if (empty($configuration['actions'])) {
            $keys = $admin
                ->getConfiguration()
                ->getActions();
            $allowedActions = array_keys($keys);

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
        $event->setConfiguration($configuration);
    }
}

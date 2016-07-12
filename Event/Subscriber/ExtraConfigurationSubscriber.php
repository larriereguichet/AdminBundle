<?php

namespace LAG\AdminBundle\Event\Subscriber;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Persistence\Mapping\MappingException;
use Doctrine\ORM\EntityManager;
use LAG\AdminBundle\Admin\Behaviors\TranslationKeyTrait;
use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Configuration\Factory\ConfigurationFactory;
use LAG\AdminBundle\Admin\Event\AdminEvent;
use LAG\AdminBundle\Admin\Event\AdminEvents;
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
     * @var bool
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
            AdminEvents::ACTION_CREATE => 'actionCreate',
        ];
    }

    /**
     * ExtraConfigurationSubscriber constructor.
     *
     * @param bool $extraConfigurationEnabled
     * @param Registry $doctrine
     * @param ConfigurationFactory $configurationFactory
     */
    public function __construct(
        $extraConfigurationEnabled = true,
        Registry $doctrine,
        ConfigurationFactory $configurationFactory
    ) {
        $this->extraConfigurationEnabled = $extraConfigurationEnabled;
        // entity manager can be closed, so its better to inject the Doctrine registry instead
        $this->entityManager = $doctrine->getManager();
        $this->applicationConfiguration = $configurationFactory->getApplicationConfiguration();
    }

    /**
     * Adding default CRUD if none is defined.
     *
     * @param AdminEvent $event
     */
    public function adminCreate(AdminEvent $event)
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
                'batch' => []
            ];
            $event->setAdminConfiguration($configuration);
        }
    }

    /**
     * Add default linked actions and default menu actions.
     *
     * @param AdminEvent $event
     * @throws MappingException
     */
    public function actionCreate(AdminEvent $event)
    {
        // add configuration only if extra configuration is enabled
        if (!$this->extraConfigurationEnabled) {
            return;
        }
        // action configuration array
        $configuration = $event->getActionConfiguration();
        // current action admin
        $admin = $event->getAdmin();
        // allowed actions according to the admin
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

        // if no field was provided in configuration, we try to take fields from doctrine metadata
        if (empty($configuration['fields']) || !count($configuration['fields'])) {
            $fields = [];
            $guesser = new FieldTypeGuesser();
            $metadata = $this
                ->entityManager
                ->getMetadataFactory()
                ->getMetadataFor($admin->getConfiguration()->getParameter('entity'));
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

                if ($event->getActionName() == 'list') {
                    $configuration['fields']['_actions'] = null;
                }
            }
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

            // add a link to the "edit" action, if it is allowed
            if (in_array('edit', $allowedActions)) {
                $configuration['fields']['_actions']['type'] = 'collection';
                $configuration['fields']['_actions']['options']['_edit'] = [
                    'type' => 'action',
                    'options' => [
                        'title' => $this->getTranslationKey($translationPattern, 'edit', $event->getAdmin()->getName()),
                        'route' => $admin->generateRouteName('edit'),
                        'parameters' => [
                            'id' => false
                        ],
                        'icon' => 'pencil'
                    ]
                ];
            }
            // add a link to the "delete" action, if it is allowed
            if (in_array('delete', $allowedActions)) {
                $configuration['fields']['_actions']['type'] = 'collection';
                $configuration['fields']['_actions']['options']['_delete'] = [
                    'type' => 'action',
                    'options' => [
                        'title' => $this->getTranslationKey($translationPattern, 'delete', $event->getAdmin()->getName()),
                        'route' => $admin->generateRouteName('delete'),
                        'parameters' => [
                            'id' => false
                        ],
                        'icon' => 'remove'
                    ]
                ];
            }
        }
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
    protected function addDefaultMenuConfiguration($adminName, $actionName, array $actionConfiguration, array $allowedActions)
    {
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
}

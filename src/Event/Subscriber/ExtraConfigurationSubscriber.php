<?php

namespace LAG\AdminBundle\Event\Subscriber;

use Doctrine\ORM\EntityManagerInterface;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Metadata\MetadataHelperInterface;
use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Configuration\ApplicationConfigurationStorage;
use LAG\AdminBundle\Event\Events;
use LAG\AdminBundle\Event\Events\ConfigurationEvent;
use LAG\AdminBundle\Event\Menu\MenuConfigurationEvent;
use LAG\AdminBundle\Factory\ConfigurationFactory;
use LAG\AdminBundle\Field\Helper\FieldConfigurationHelper;
use LAG\AdminBundle\Resource\ResourceCollection;
use LAG\AdminBundle\Utils\TranslationUtils;
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
     * @var ResourceCollection
     */
    private $resourceCollection;

    /**
     * @var ConfigurationFactory
     */
    private $configurationFactory;

    /**
     * @var MetadataHelperInterface
     */
    private $metadataHelper;

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
            Events::CONFIGURATION_ADMIN => 'enrichAdminConfiguration',
            Events::MENU_CONFIGURATION => 'enrichMenuConfiguration',
        ];
    }

    /**
     * ExtraConfigurationSubscriber constructor.
     *
     * @param ApplicationConfigurationStorage $applicationConfigurationStorage
     * @param EntityManagerInterface          $entityManager
     * @param ResourceCollection              $resourceCollection
     * @param ConfigurationFactory            $configurationFactory
     * @param MetadataHelperInterface         $metadataHelper
     */
    public function __construct(
        ApplicationConfigurationStorage $applicationConfigurationStorage,
        EntityManagerInterface $entityManager,
        ResourceCollection $resourceCollection,
        ConfigurationFactory $configurationFactory,
        MetadataHelperInterface $metadataHelper
    ) {
        $this->applicationConfiguration = $applicationConfigurationStorage->getConfiguration();
        $this->resourceCollection = $resourceCollection;
        $this->configurationFactory = $configurationFactory;
        $this->metadataHelper = $metadataHelper;
        $this->entityManager = $entityManager;
    }

    public function enrichAdminConfiguration(ConfigurationEvent $event)
    {
        if (!$this->isExtraConfigurationEnabled()) {
            return;
        }
        $configuration = $event->getConfiguration();

        // Actions
        $this->addDefaultActions($configuration);

        // Add default field configuration: it provides a type, a form type, and a view according to the found metadata
        $helper = new FieldConfigurationHelper($this->entityManager, $this->applicationConfiguration, $this->metadataHelper);
        $helper->addDefaultFields($configuration, $event->getEntityClass(), $event->getAdminName());
        $helper->addDefaultStrategy($configuration);
        $helper->addDefaultRouteParameters($configuration);
        $helper->addDefaultFormUse($configuration);
        $helper->provideActionsFieldConfiguration($configuration, $event->getAdminName());

        // Menus
        $this->addDefaultRightMenu($configuration);
        $this->addDefaultLeftMenu($configuration);
        $this->addDefaultTopMenu($configuration, $event->getAdminName());

        // Filters
        $this->addDefaultFilters($configuration);

        $event->setConfiguration($configuration);
    }

    public function enrichMenuConfiguration(MenuConfigurationEvent $event)
    {
        if (!$this->isExtraConfigurationEnabled()) {
            return;
        }
        $configuration = $event->getMenuConfigurations();

        if (!key_exists('top', $configuration) || [] === $configuration['top']) {
            $configuration['top'] = [
                'brand' => $this->applicationConfiguration->getParameter('title'),
            ];
        }

        if (!key_exists('left', $configuration) || [] === $configuration['left']) {
            $configuration['left'] = $this->configurationFactory->createResourceMenuConfiguration();
        }

        $event->setMenuConfigurations($configuration);
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

    /**
     * Add the default left menu configuration. One item for each Admin.
     *
     * @param array $configuration
     */
    private function addDefaultLeftMenu(array &$configuration)
    {
        if (!$this->applicationConfiguration->getParameter('enable_menus')) {
            return;
        }
        $menus = $this->configurationFactory->createResourceMenuConfiguration();

        // Add the resources menu for each action of the admin
        foreach ($configuration['actions'] as $name => $action) {
            if (null === $action) {
                $action = [];
            }

            if (key_exists('menus', $action) && key_exists('left', $action)) {
                continue;
            }

            $configuration['actions'][$name]['menus']['left'] = $menus;
        }
    }

    /**
     * Add the default right menu.
     *
     * @param array  $configuration
     */
    private function addDefaultRightMenu(array &$configuration)
    {
        if (!$this->applicationConfiguration->getParameter('enable_menus')) {
            return;
        }

        if (!key_exists('list', $configuration['actions'])) {
            return;
        }

        if (
            key_exists('menus', $configuration['actions']['list']) &&
            is_array($configuration['actions']['list']['menus']) &&
            key_exists('right', $configuration['actions']['list']['menus'])
        ) {
            return;
        }

        $configuration['actions']['list']['menus']['right'] = [];
    }

    private function addDefaultTopMenu(array &$configuration, string $adminName)
    {
        if (!$this->applicationConfiguration->getParameter('enable_menus')) {
            return;
        }

        if (key_exists('list', $configuration['actions'])) {
            // Add a "Create" link in the top bar if the create action is allowed
            if (!key_exists('create', $configuration['actions'])) {
                return;
            }
            $configuration['actions']['list']['menus']['top']['items'][] = [
                'admin' => $adminName,
                'action' => 'create',
                'text' => TranslationUtils::getTranslationKey(
                    $this->applicationConfiguration->getParameter('translation_pattern'),
                    $adminName,
                    'create'
                ),
                'icon' => 'plus',
            ];
        }

        if (key_exists('create', $configuration['actions'])) {
            // Add a "Return" link in the top bar if the list action is allowed
            if (!key_exists('list', $configuration['actions'])) {
                return;
            }
            $configuration['actions']['create']['menus']['top']['items'][] = [
                'admin' => $adminName,
                'action' => 'list',
                'text' => TranslationUtils::getTranslationKey(
                    $this->applicationConfiguration->getParameter('translation_pattern'),
                    $adminName,
                    'return'
                ),
                'icon' => 'arrow-left',
            ];
        }

        if (key_exists('edit', $configuration['actions'])) {
            // Add a "Return" link in the top bar if the list action is allowed
            if (!key_exists('list', $configuration['actions'])) {
                return;
            }

            if (!key_exists('menus', $configuration['actions']['edit'])) {
                $configuration['actions']['edit']['menus'] = [];
            }

            if (!key_exists('top', $configuration['actions']['edit']['menus'])) {
                $configuration['actions']['edit']['menus']['top'] = [];
            }

            if (!key_exists('items', $configuration['actions']['edit']['menus']['top'])) {
                $configuration['actions']['edit']['menus']['top']['items'] = [];
            }
            array_unshift($configuration['actions']['edit']['menus']['top']['items'], [
                'admin' => $adminName,
                'action' => 'list',
                'text' => TranslationUtils::getTranslationKey(
                    $this->applicationConfiguration->getParameter('translation_pattern'),
                    $adminName,
                    'return'
                ),
                'icon' => 'arrow-left',
            ]);
        }
    }

    /**
     * Add default filters for the list actions, guessed using the entity metadata.
     *
     * @param array $configuration
     */
    private function addDefaultFilters(array &$configuration)
    {
        // Add the filters only for the "list" action
        if (!key_exists('list', $configuration['actions'])) {
            return;
        }

        // If some filters are already configured, we do not add the default filters
        if (key_exists('filter', $configuration['actions']['list'])) {
            return;
        }
        // TODO add a default unified filter
        $metadata = $this->metadataHelper->findMetadata($configuration['entity']);

        if (null === $metadata) {
            return;
        }
        $filters = [];

        foreach ($metadata->getFieldNames() as $fieldName) {
            $type = $metadata->getTypeOfField($fieldName);
            $operator = $this->getOperatorFromFieldType($type);

            $filters[$fieldName] = [
                'type' => $type,
                'options' => [],
                'comparator' => $operator,
            ];
        }
        $configuration['actions']['list']['filters'] = $filters;
    }

    private function getOperatorFromFieldType($type)
    {
        $mapping = [
            'string' => 'like',
            'text' => 'like',
        ];

        if (key_exists($type, $mapping)) {
            return $mapping[$type];
        }

        return '=';
    }

    /**
     * @return bool
     */
    private function isExtraConfigurationEnabled(): bool
    {
        return $this->applicationConfiguration->getParameter('enable_extra_configuration');
    }
}

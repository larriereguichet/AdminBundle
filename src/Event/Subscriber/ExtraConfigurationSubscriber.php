<?php

namespace LAG\AdminBundle\Event\Subscriber;

use Doctrine\ORM\EntityManagerInterface;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Metadata\MetadataHelperInterface;
use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Configuration\ApplicationConfigurationStorage;
use LAG\AdminBundle\Event\Events;
use LAG\AdminBundle\Event\Events\ConfigurationEvent;
use LAG\AdminBundle\Factory\ConfigurationFactory;
use LAG\AdminBundle\Field\Helper\FieldConfigurationHelper;
use LAG\AdminBundle\Resource\Registry\ResourceRegistryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

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
     * @var ResourceRegistryInterface
     */
    private $registry;

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
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::CONFIGURATION_ADMIN => 'enrichAdminConfiguration',
        ];
    }

    /**
     * ExtraConfigurationSubscriber constructor.
     */
    public function __construct(
        ApplicationConfigurationStorage $applicationConfigurationStorage,
        EntityManagerInterface $entityManager,
        ResourceRegistryInterface $registry,
        ConfigurationFactory $configurationFactory,
        MetadataHelperInterface $metadataHelper,
        TranslatorInterface $translator
    ) {
        $this->applicationConfiguration = $applicationConfigurationStorage->getConfiguration();
        $this->registry = $registry;
        $this->configurationFactory = $configurationFactory;
        $this->metadataHelper = $metadataHelper;
        $this->entityManager = $entityManager;
        $this->translator = $translator;
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
        $helper = new FieldConfigurationHelper(
            $this->entityManager,
            $this->translator,
            $this->applicationConfiguration,
            $this->metadataHelper
        );
        $helper->addDefaultFields($configuration, $event->getEntityClass(), $event->getAdminName());
        $helper->addDefaultStrategy($configuration);
        $helper->addDefaultRouteParameters($configuration);
        $helper->addDefaultFormUse($configuration);
        $helper->provideActionsFieldConfiguration($configuration, $event->getAdminName());

        // Filters
        $this->addDefaultFilters($configuration);

        $event->setConfiguration($configuration);
    }

    /**
     * Defines the default CRUD actions if no action was configured.
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
     * Add default filters for the list actions, guessed using the entity metadata.
     */
    private function addDefaultFilters(array &$configuration): void
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

    private function isExtraConfigurationEnabled(): bool
    {
        return $this->applicationConfiguration->getParameter('enable_extra_configuration');
    }
}

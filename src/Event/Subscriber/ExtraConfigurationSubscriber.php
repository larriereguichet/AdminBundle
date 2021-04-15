<?php

namespace LAG\AdminBundle\Event\Subscriber;

use LAG\AdminBundle\Bridge\Doctrine\ORM\Metadata\MetadataHelperInterface;
use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Event\AdminEvents;
use LAG\AdminBundle\Event\Events\Configuration\AdminConfigurationEvent;
use LAG\AdminBundle\Field\Helper\FieldConfigurationHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Add extra default configuration for actions and fields.
 */
// TODO remove this class
class ExtraConfigurationSubscriber implements EventSubscriberInterface
{
    private ApplicationConfiguration $applicationConfiguration;
    private MetadataHelperInterface $metadataHelper;
    private TranslatorInterface $translator;

    public static function getSubscribedEvents(): array
    {
        return [
            AdminEvents::ADMIN_CONFIGURATION => 'enrichAdminConfiguration',
        ];
    }

    public function __construct(
        ApplicationConfiguration $applicationConfiguration,
        MetadataHelperInterface $metadataHelper,
        TranslatorInterface $translator
    ) {
        $this->applicationConfiguration = $applicationConfiguration;
        $this->metadataHelper = $metadataHelper;
        $this->translator = $translator;
    }

    public function enrichAdminConfiguration(AdminConfigurationEvent $event): void
    {
        if (!$this->isExtraConfigurationEnabled()) {
            return;
        }
        $configuration = $event->getConfiguration();

        // Actions
        $this->addDefaultActions($configuration);

        // Add default field configuration: it provides a type, a form type, and a view according to the found metadata
        $helper = new FieldConfigurationHelper(
            $this->translator,
            $this->applicationConfiguration,
            $this->metadataHelper
        );
        //$helper->addDefaultFields($configuration, $configuration['entity'], $event->getAdminName());
        $helper->addDefaultStrategy($configuration);
        $helper->addDefaultRouteParameters($configuration);
        //$helper->addDefaultFormUse($configuration);
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
        if (!\array_key_exists('actions', $configuration) || !\is_array($configuration['actions'])) {
            $configuration['actions'] = [];
        }

        if (0 !== \count($configuration['actions'])) {
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
        if (!\array_key_exists('list', $configuration['actions'])) {
            return;
        }

        // If some filters are already configured, we do not add the default filters
        if (\array_key_exists('filter', $configuration['actions']['list'])) {
            return;
        }
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

    private function getOperatorFromFieldType($type): string
    {
        $mapping = [
            'string' => 'like',
            'text' => 'like',
        ];

        if (\array_key_exists($type, $mapping)) {
            return $mapping[$type];
        }

        return '=';
    }

    private function isExtraConfigurationEnabled(): bool
    {
        // TODO
        return true;
    }
}

<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event\Subscriber;

use LAG\AdminBundle\Admin\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Metadata\MetadataHelperInterface;
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
        //$this->addDefaultFilters($configuration);

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

    private function isExtraConfigurationEnabled(): bool
    {
        // TODO
        return true;
    }
}

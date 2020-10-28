<?php

namespace LAG\AdminBundle\Factory\Configuration;

use Exception;
use LAG\AdminBundle\Configuration\AdminConfiguration;
use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Event\AdminEvents;
use LAG\AdminBundle\Event\Events\Configuration\AdminConfigurationEvent;
use LAG\AdminBundle\Exception\ConfigurationException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class AdminConfigurationFactory implements AdminConfigurationFactoryInterface
{
    private ApplicationConfiguration $applicationConfiguration;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(ApplicationConfiguration $applicationConfiguration, EventDispatcherInterface $eventDispatcher)
    {
        $this->applicationConfiguration = $applicationConfiguration;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function create(string $adminName, array $options = []): AdminConfiguration
    {
        $event = new AdminConfigurationEvent($adminName, $options);
        $this->eventDispatcher->dispatch($event, AdminEvents::ADMIN_CONFIGURATION);
        $configuration = new AdminConfiguration();
        $values = array_merge($this->getDefaultConfiguration(), $event->getConfiguration());

        try {
            $configuration->configure($values);
        } catch (Exception $exception) {
            throw new ConfigurationException('admin', $adminName, $exception);
        }

        return $configuration;
    }

    private function getDefaultConfiguration(): array
    {
        return [
            'class' => $this->applicationConfiguration->getAdminClass(),
            'routes_pattern' => $this->applicationConfiguration->getRoutesPattern(),
            'max_per_page' => $this->applicationConfiguration->getMaxPerPage(),
            'pager' => $this->applicationConfiguration->isTranslationEnabled()
                ? $this->applicationConfiguration->getPager()
                : false,
            'permissions' => $this->applicationConfiguration->getPermissions(),
            'string_length' => $this->applicationConfiguration->getStringLength(),
            'string_truncate' => $this->applicationConfiguration->getStringTruncate(),
            'date_format' => $this->applicationConfiguration->getDateFormat(),
            'page_parameter' => $this->applicationConfiguration->getPageParameter(),
            'list_template' => $this->applicationConfiguration->getListTemplate(),
            'edit_template' => $this->applicationConfiguration->getEditTemplate(),
            'create_template' => $this->applicationConfiguration->getCreateTemplate(),
            'delete_template' => $this->applicationConfiguration->getDeleteTemplate(),
            'menus' => $this->applicationConfiguration->getMenus(),
        ];
    }
}

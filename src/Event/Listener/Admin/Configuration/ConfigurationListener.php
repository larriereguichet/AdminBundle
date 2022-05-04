<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event\Listener\Admin\Configuration;

use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Event\Events\Configuration\AdminConfigurationEvent;

class ConfigurationListener
{
    private ApplicationConfiguration $applicationConfiguration;

    public function __construct(ApplicationConfiguration $applicationConfiguration)
    {
        $this->applicationConfiguration = $applicationConfiguration;
    }

    public function __invoke(AdminConfigurationEvent $event): void
    {
        $configuration = $event->getConfiguration();

        if (empty($configuration['name'])) {
            $configuration['name'] = $event->getAdminName();
        }
        $configuration = array_merge($this->getDefaultConfiguration(), $configuration);

        if (!empty($configuration['actions']) && is_iterable($configuration['actions'])) {
            foreach ($configuration['actions'] as $actionName => $actionConfiguration) {
                if ($actionConfiguration['admin_name'] ?? false) {
                    $configuration['actions'][$actionName]['admin_name'] = $event->getAdminName();
                }

                if ($actionConfiguration['template'] ?? false) {
                    $template = $this->getDefaultTemplate($actionName, $configuration);
                    $configuration['actions'][$actionName]['template'] = $template;
                }
            }
        }
        $event->setConfiguration($configuration);
    }

    private function getDefaultConfiguration(): array
    {
        return [
            'admin_class' => $this->applicationConfiguration->getAdminClass(),
            'routes_pattern' => $this->applicationConfiguration->getRoutesPattern(),
            'max_per_page' => $this->applicationConfiguration->getMaxPerPage(),
            'pager' => $this->applicationConfiguration->isTranslationEnabled()
                ? $this->applicationConfiguration->getPager()
                : false,
            'permissions' => $this->applicationConfiguration->getPermissions(),
            'date_format' => $this->applicationConfiguration->getDateFormat(),
            'page_parameter' => $this->applicationConfiguration->getPageParameter(),
            'list_template' => $this->applicationConfiguration->getListTemplate(),
            'update_template' => $this->applicationConfiguration->getUpdateTemplate(),
            'create_template' => $this->applicationConfiguration->getCreateTemplate(),
            'delete_template' => $this->applicationConfiguration->getDeleteTemplate(),
        ];
    }

    private function getDefaultTemplate(string $actionName, array $adminConfiguration): string
    {
        if (!empty($adminConfiguration[$actionName.'_template'])) {
            return $adminConfiguration[$actionName.'_template'];
        }
        $map = [
            'list_template' => $this->applicationConfiguration->getListTemplate(),
            'update_template' => $this->applicationConfiguration->getUpdateTemplate(),
            'create_template' => $this->applicationConfiguration->getCreateTemplate(),
            'delete_template' => $this->applicationConfiguration->getDeleteTemplate(),
        ];

        return \array_key_exists($actionName.'_template', $map) ? $map[$actionName.'_template'] : '';
    }
}

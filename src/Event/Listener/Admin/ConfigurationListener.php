<?php

namespace LAG\AdminBundle\Event\Listener\Admin;

use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Event\Events\Configuration\AdminConfigurationEvent;

class ConfigurationListener
{
    private ApplicationConfiguration $appConfig;

    public function __construct(ApplicationConfiguration $appConfig)
    {
        $this->appConfig = $appConfig;
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
                if (empty($actionConfiguration['admin_name'])) {
                    $configuration['actions'][$actionName]['admin_name'] = $event->getAdminName();
                }

                if (empty($actionConfiguration['template'])) {
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
            'admin_class' => $this->appConfig->getAdminClass(),
            'routes_pattern' => $this->appConfig->getRoutesPattern(),
            'max_per_page' => $this->appConfig->getMaxPerPage(),
            'pager' => $this->appConfig->isTranslationEnabled()
                ? $this->appConfig->getPager()
                : false,
            'permissions' => $this->appConfig->getPermissions(),
            'date_format' => $this->appConfig->getDateFormat(),
            'page_parameter' => $this->appConfig->getPageParameter(),
            'list_template' => $this->appConfig->getListTemplate(),
            'edit_template' => $this->appConfig->getEditTemplate(),
            'create_template' => $this->appConfig->getCreateTemplate(),
            'delete_template' => $this->appConfig->getDeleteTemplate(),
            'menus' => $this->appConfig->getMenus(),
        ];
    }

    private function getDefaultTemplate(string $actionName, array $adminConfiguration): string
    {
        if (!empty($adminConfiguration[$actionName.'_template'])) {
            return $adminConfiguration[$actionName.'_template'];
        }
        $map = [
            'list_template' => $this->appConfig->getListTemplate(),
            'edit_template' => $this->appConfig->getEditTemplate(),
            'create_template' => $this->appConfig->getCreateTemplate(),
            'delete_template' => $this->appConfig->getDeleteTemplate(),
        ];

        return key_exists($actionName.'_template', $map) ? $map[$actionName.'_template'] : '';
    }
}

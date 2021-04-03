<?php

namespace LAG\AdminBundle\Admin\Configuration;

use LAG\AdminBundle\Configuration\AdminConfiguration;

class ActionConfigurationMapper
{
    public function map(string $actionName, AdminConfiguration $configuration): array
    {
        return [
            'admin_name' => $configuration->getName(),
            'action_class' => $configuration->getActionClass(),
            'max_per_page' => $configuration->getMaxPerPage(),
            'pager' => $configuration->isTranslationEnabled()
                ? $configuration->getPager()
                : false,
            'permissions' => $configuration->getPermissions(),
            'date_format' => $configuration->getDateFormat(),
            'page_parameter' => $configuration->getPageParameter(),
            'template' => $this->getDefaultTemplate($actionName, $configuration),
            'menus' => $configuration->getMenus(),
            'fields' => [],
        ];
    }

    private function getDefaultTemplate(string $name, AdminConfiguration $adminConfiguration): string
    {
        $map = [
            'list_template' => $adminConfiguration->getListTemplate(),
            'edit_template' => $adminConfiguration->getEditTemplate(),
            'create_template' => $adminConfiguration->getCreateTemplate(),
            'delete_template' => $adminConfiguration->getDeleteTemplate(),
        ];

        return key_exists($name.'_template', $map) ? $map[$name.'_template'] : '';
    }
}

<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Configuration;

use Closure;
use JK\Configuration\ServiceConfiguration;
use LAG\AdminBundle\Admin\Action;
use LAG\AdminBundle\Admin\Admin;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Field\ActionCollectionField;
use LAG\AdminBundle\Field\ActionField;
use LAG\AdminBundle\Field\ArrayField;
use LAG\AdminBundle\Field\AutoField;
use LAG\AdminBundle\Field\BooleanField;
use LAG\AdminBundle\Field\CountField;
use LAG\AdminBundle\Field\DateField;
use LAG\AdminBundle\Field\FieldInterface;
use LAG\AdminBundle\Field\LinkField;
use LAG\AdminBundle\Field\MappedField;
use LAG\AdminBundle\Field\StringField;
use LAG\AdminBundle\Translation\Helper\TranslationHelper;
use LAG\AdminBundle\Translation\Helper\TranslationHelperInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function Symfony\Component\String\u;

/**
 * Application configuration class. Allow easy configuration manipulation within an Admin.
 */
class ApplicationConfiguration extends ServiceConfiguration
{
    public const FIELD_MAPPING = [
        FieldInterface::TYPE_STRING => StringField::class,
        FieldInterface::TYPE_TEXT => StringField::class,
        FieldInterface::TYPE_FLOAT => StringField::class,
        FieldInterface::TYPE_INTEGER => StringField::class,
        FieldInterface::TYPE_ARRAY => ArrayField::class,
        FieldInterface::TYPE_ACTION => ActionField::class,
        FieldInterface::TYPE_BOOLEAN => BooleanField::class,
        FieldInterface::TYPE_MAPPED => MappedField::class,
        FieldInterface::TYPE_ACTION_COLLECTION => ActionCollectionField::class,
        FieldInterface::TYPE_LINK => LinkField::class,
        FieldInterface::TYPE_DATE => DateField::class,
        FieldInterface::TYPE_COUNT => CountField::class,
        FieldInterface::TYPE_AUTO => AutoField::class,
    ];

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('title', 'Admin Application')
            ->setAllowedTypes('title', 'string')
            ->setDefault('description', 'Admin Application')
            ->setAllowedTypes('description', 'string')

            // Admins
            ->setRequired('resources_path')
            ->setAllowedTypes('resources_path', 'string')
            ->setDefault('admin_class', Admin::class)
            ->setAllowedTypes('admin_class', 'string')
            ->setDefault('action_class', Action::class)
            ->setAllowedTypes('action_class', 'string')

            // Templates
            ->setDefault('base_template', '@LAGAdmin/base.html.twig')
            ->setAllowedTypes('base_template', 'string')
            ->setDefault('ajax_template', '@LAGAdmin/empty.html.twig')
            ->setAllowedTypes('ajax_template', 'string')
            ->setDefault('menu_template', '@LAGAdmin/menu/menu.html.twig')
            ->setAllowedTypes('menu_template', 'string')
            ->setDefault('create_template', '@LAGAdmin/crud/create.html.twig')
            ->setAllowedTypes('create_template', 'string')
            ->setDefault('edit_template', '@LAGAdmin/crud/edit.html.twig')
            ->setAllowedTypes('edit_template', 'string')
            ->setDefault('list_template', '@LAGAdmin/crud/list.html.twig')
            ->setAllowedTypes('list_template', 'string')
            ->setDefault('delete_template', '@LAGAdmin/crud/delete.html.twig')
            ->setAllowedTypes('delete_template', 'string')

            // Routing
            ->setDefault('routes_pattern', 'lag_admin.{admin}.{action}')
            ->setAllowedTypes('routes_pattern', 'string')
            ->setNormalizer('routes_pattern', $this->getRoutesPatternNormalizer())
            ->setDefault('homepage_route', 'lag_admin.homepage')
            ->setAllowedTypes('homepage_route', 'string')

            // Dates
            ->setDefault('date_format', 'Y/m/d')
            ->setAllowedTypes('date_format', 'string')

            // Pagination
            ->setDefault('pager', 'pagerfanta')
            ->setAllowedTypes('pager', ['boolean', 'string'])
            ->setDefault('max_per_page', 25)
            ->setAllowedTypes('max_per_page', 'integer')
            ->setDefault('page_parameter', 'page')
            ->setAllowedTypes('page_parameter', 'string')

            // List default parameters
            ->setDefault('string_length', 100)
            ->setAllowedTypes('string_length', 'integer')
            ->setDefault('string_truncate', '...')
            ->setAllowedTypes('string_truncate', 'string')

            // Default permissions
            ->setDefault('enable_security', true)
            ->setAllowedTypes('enable_security', 'boolean')
            ->setDefault('permissions', 'ROLE_ADMIN')
            ->setAllowedTypes('permissions', 'string')

            // Translation
            ->setDefault('translation', function (OptionsResolver $translationResolver) {
                $translationResolver
                    ->setDefault('enabled', true)
                    ->setAllowedTypes('enabled', 'boolean')
                    ->setDefault('pattern', 'admin.{admin}.{key}')
                    ->setAllowedTypes('pattern', 'string')
                    ->setDefault('catalog', 'admin')
                    ->setAllowedTypes('catalog', 'string')
                ;
            })

            // Fields default mapping
            ->setDefault('fields_mapping', [])
            ->setNormalizer('fields_mapping', $this->getFieldsMappingNormalizer())

            ->setDefault('menus', [])
            ->setAllowedTypes('menus', ['array', 'null'])
            ->setNormalizer('menus', $this->getMenusNormalizer())
        ;
    }

    public function getTitle(): string
    {
        return $this->getString('title');
    }

    public function getDescription(): string
    {
        return $this->getString('description');
    }

    public function getResourcesPath(): string
    {
        return $this->getString('resources_path');
    }

    public function getAdminClass(): string
    {
        return $this->getString('admin_class');
    }

    public function getActionClass(): string
    {
        return $this->getString('action_class');
    }

    public function getBaseTemplate(): string
    {
        return $this->getString('base_template');
    }

    public function getAjaxTemplate(): string
    {
        return $this->getString('ajax_template');
    }

    public function getMenuTemplate(): string
    {
        return $this->getString('menu_template');
    }

    public function getCreateTemplate(): string
    {
        return $this->getString('create_template');
    }

    public function getListTemplate(): string
    {
        return $this->getString('list_template');
    }

    public function getEditTemplate(): string
    {
        return $this->getString('edit_template');
    }

    public function getDeleteTemplate(): string
    {
        return $this->getString('delete_template');
    }

    public function getRoutesPattern(): string
    {
        return $this->get('routes_pattern');
    }

    public function getHomepageRoute(): string
    {
        return $this->getString('homepage_route');
    }

    public function getDateFormat(): string
    {
        return $this->getString('date_format');
    }

    public function isPaginationEnabled(): bool
    {
        $pager = $this->get('pager');

        if ($pager === false) {
            return false;
        }

        return true;
    }

    public function getPager(): string
    {
        if (!$this->isPaginationEnabled()) {
            throw new Exception('The pagination is not enabled');
        }

        return $this->getString('pager');
    }

    public function getMaxPerPage(): int
    {
        return $this->getInt('max_per_page');
    }

    public function getPageParameter(): string
    {
        return $this->getString('page_parameter');
    }

    public function getStringLength(): int
    {
        return $this->getInt('string_length');
    }

    public function getStringTruncate(): string
    {
        return $this->getString('string_truncate');
    }

    public function getPermissions(): string
    {
        return $this->getString('permissions');
    }

    public function isTranslationEnabled(): bool
    {
        return $this->get('translation')['enabled'];
    }

    public function getTranslationPattern(): string
    {
        if (!$this->isTranslationEnabled()) {
            throw new Exception('The translation is not enabled');
        }

        return $this->get('translation')['pattern'];
    }

    public function getTranslationKey(string $admin, string $key): string
    {
        if (!$this->isTranslationEnabled()) {
            throw new Exception('The translation is not enabled');
        }

        return TranslationHelper::getTranslationKey($this->getTranslationPattern(), $admin, $key);
    }

    public function getTranslationCatalog(): string
    {
        if (!$this->isTranslationEnabled()) {
            throw new Exception('The translation is not enabled');
        }

        return $this->get('translation')['catalog'];
    }

    public function getFieldsMapping(): array
    {
        return $this->get('fields_mapping');
    }

    public function getMenus(): array
    {
        return $this->get('menus');
    }

    public function getRouteName(string $adminName, string $actionName): string
    {
        $routeName = str_replace(
            '{admin}',
            strtolower($adminName),
            $this->getRoutesPattern()
        );

        return str_replace(
            '{action}',
            $actionName,
            $routeName
        );
    }

    public function isSecurityEnabled(): bool
    {
        return $this->getBool('enable_security');
    }

    private function getRoutesPatternNormalizer(): Closure
    {
        return function (Options $options, $value) {
            if (!u($value)->containsAny('{admin}')) {
                throw new InvalidOptionsException('The routes pattern should contains "{admin}" placeholder. Given '.$value);
            }

            if (!u($value)->containsAny('{action}')) {
                throw new InvalidOptionsException('The routes pattern should contains "{action}" placeholder. Given '.$value);
            }

            return $value;
        };
    }

    private function getFieldsMappingNormalizer(): Closure
    {
        return function (Options $options, $value) {
            if (!\is_array($value)) {
                $value = [];
            }

            return array_merge(self::FIELD_MAPPING, $value);
        };
    }

    private function getMenusNormalizer(): Closure
    {
        return function (Options $options, $value) {
            if ($value === null) {
                $value = [];
            }

            return $value;
        };
    }
}

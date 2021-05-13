<?php

namespace LAG\AdminBundle\Tests\Configuration;

use JK\Configuration\Exception\InvalidConfigurationException;
use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Tests\TestCase;

class ApplicationConfigurationTest extends TestCase
{
    public function testService(): void
    {
        $this->assertServiceExists(ApplicationConfiguration::class);
    }

    public function testConfigurationWithDefaultConfiguration(): void
    {
        $configuration = new ApplicationConfiguration([
            'resources_path' => 'test/',
        ]);

        $this->assertEquals([
            'title' => 'Admin Application',
            'description' => 'Admin Application',
            'admin_class' => 'LAG\AdminBundle\Admin\Admin',
            'action_class' => 'LAG\AdminBundle\Admin\Action',
            'base_template' => '@LAGAdmin/base.html.twig',
            'ajax_template' => '@LAGAdmin/empty.html.twig',
            'menu_template' => '@LAGAdmin/menu/menu.html.twig',
            'create_template' => '@LAGAdmin/crud/create.html.twig',
            'edit_template' => '@LAGAdmin/crud/edit.html.twig',
            'list_template' => '@LAGAdmin/crud/list.html.twig',
            'delete_template' => '@LAGAdmin/crud/delete.html.twig',
            'routes_pattern' => 'lag_admin.{admin}.{action}',
            'homepage_route' => 'lag_admin.homepage',
            'date_format' => 'Y/m/d',
            'pager' => 'pagerfanta',
            'max_per_page' => 25,
            'page_parameter' => 'page',
            'string_length' => 100,
            'string_truncate' => '...',
            'permissions' => 'ROLE_ADMIN',
            'translation' => [
                'enabled' => true,
                'pattern' => 'admin.{admin}.{key}',
                'catalog' => 'admin',
            ],
            'fields_mapping' => [
                'string' => 'LAG\AdminBundle\Field\StringField',
                'text' => 'LAG\AdminBundle\Field\StringField',
                'float' => 'LAG\AdminBundle\Field\StringField',
                'integer' => 'LAG\AdminBundle\Field\StringField',
                'array' => 'LAG\AdminBundle\Field\ArrayField',
                'action' => 'LAG\AdminBundle\Field\ActionField',
                'boolean' => 'LAG\AdminBundle\Field\BooleanField',
                'mapped' => 'LAG\AdminBundle\Field\MappedField',
                'action_collection' => 'LAG\AdminBundle\Field\ActionCollectionField',
                'link' => 'LAG\AdminBundle\Field\LinkField',
                'date' => 'LAG\AdminBundle\Field\DateField',
                'count' => 'LAG\AdminBundle\Field\CountField',
                'auto' => 'LAG\AdminBundle\Field\AutoField',
            ],
            'menus' => [],
            'enable_security' => true,
            'resources_path' => 'test/',
        ], $configuration->toArray());
    }

    public function testGetters(): void
    {
        $configuration = new ApplicationConfiguration([
            'resources_path' => 'test/',
        ]);

        $this->assertEquals('Admin Application', $configuration->getTitle());
        $this->assertEquals('Admin Application', $configuration->getDescription());
        $this->assertEquals('LAG\AdminBundle\Admin\Admin', $configuration->getAdminClass());
        $this->assertEquals('LAG\AdminBundle\Admin\Action', $configuration->getActionClass());

        $this->assertEquals('@LAGAdmin/base.html.twig', $configuration->getBaseTemplate());
        $this->assertEquals('@LAGAdmin/empty.html.twig', $configuration->getAjaxTemplate());
        $this->assertEquals('@LAGAdmin/menu/menu.html.twig', $configuration->getMenuTemplate());
        $this->assertEquals('@LAGAdmin/crud/create.html.twig', $configuration->getCreateTemplate());
        $this->assertEquals('@LAGAdmin/crud/edit.html.twig', $configuration->getEditTemplate());
        $this->assertEquals('@LAGAdmin/crud/list.html.twig', $configuration->getListTemplate());
        $this->assertEquals('@LAGAdmin/crud/delete.html.twig', $configuration->getDeleteTemplate());

        $this->assertEquals('lag_admin.{admin}.{action}', $configuration->getRoutesPattern());
        $this->assertEquals('lag_admin.homepage', $configuration->getHomepageRoute());

        $this->assertEquals('Y/m/d', $configuration->getDateFormat());

        $this->assertTrue($configuration->isPaginationEnabled());
        $this->assertEquals('pagerfanta', $configuration->getPager());
        $this->assertEquals(25, $configuration->getMaxPerPage());
        $this->assertEquals('page', $configuration->getPageParameter());

        $this->assertEquals(100, $configuration->getStringLength());
        $this->assertEquals('...', $configuration->getStringTruncate());

        $this->assertEquals('ROLE_ADMIN', $configuration->getPermissions());

        $this->assertEquals(true, $configuration->isTranslationEnabled());
        $this->assertEquals('admin.{admin}.{key}', $configuration->getTranslationPattern());
        $this->assertEquals('admin', $configuration->getTranslationCatalog());

        $this->assertEquals([
            'string' => 'LAG\AdminBundle\Field\StringField',
            'text' => 'LAG\AdminBundle\Field\StringField',
            'float' => 'LAG\AdminBundle\Field\StringField',
            'integer' => 'LAG\AdminBundle\Field\StringField',
            'array' => 'LAG\AdminBundle\Field\ArrayField',
            'action' => 'LAG\AdminBundle\Field\ActionField',
            'boolean' => 'LAG\AdminBundle\Field\BooleanField',
            'mapped' => 'LAG\AdminBundle\Field\MappedField',
            'action_collection' => 'LAG\AdminBundle\Field\ActionCollectionField',
            'link' => 'LAG\AdminBundle\Field\LinkField',
            'date' => 'LAG\AdminBundle\Field\DateField',
            'count' => 'LAG\AdminBundle\Field\CountField',
            'auto' => 'LAG\AdminBundle\Field\AutoField',
        ], $configuration->getFieldsMapping());

        $this->assertEquals([], $configuration->getMenus());
        $this->assertEquals('test/', $configuration->getResourcesPath());

        $this->assertTrue($configuration->isSecurityEnabled());
    }

    public function testConfigurationWithoutConfiguration(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        new ApplicationConfiguration([]);
    }

    public function testWithoutPagination(): void
    {
        $configuration = new ApplicationConfiguration([
            'resources_path' => 'test/',
            'pager' => false,
        ]);

        $this->assertFalse($configuration->isPaginationEnabled());

        $this->expectException(Exception::class);
        $configuration->getPager();
    }

    public function testWithoutTranslation(): void
    {
        $configuration = new ApplicationConfiguration([
            'resources_path' => 'test/',
            'translation' => ['enabled' => false],
        ]);
        $this->assertFalse($configuration->isTranslationEnabled());

        $this->expectException(Exception::class);
        $configuration->getTranslationCatalog();
    }

    public function testWithoutTranslationPattern(): void
    {
        $configuration = new ApplicationConfiguration([
            'resources_path' => 'test/',
            'translation' => ['enabled' => false],
        ]);
        $this->assertFalse($configuration->isTranslationEnabled());

        $this->expectException(Exception::class);
        $configuration->getTranslationPattern();
    }

    public function testGetRouteName(): void
    {
        $configuration = new ApplicationConfiguration(['resources_path' => 'test/']);

        $this->assertEquals('lag_admin.panda.bamboo', $configuration->getRouteName('panda', 'bamboo'));
    }

    public function testWithoutAdminPlaceHolder(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        new ApplicationConfiguration([
            'resources_path' => 'test/',
            'routes_pattern' => 'test',
        ]);
    }

    public function testWithoutActionPlaceHolder(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        new ApplicationConfiguration([
            'resources_path' => 'test/',
            'routes_pattern' => 'test.{admin}',
        ]);
    }

    public function testFieldsMappingNormalizer(): void
    {
        $configuration = new ApplicationConfiguration([
            'resources_path' => 'test/',
            'fields_mapping' => null,
        ]);

        $this->assertEquals(ApplicationConfiguration::FIELD_MAPPING, $configuration->getFieldsMapping());
    }

    public function testMenuNormalizer(): void
    {
        $configuration = new ApplicationConfiguration([
            'resources_path' => 'test/',
            'menus' => null,
        ]);

        $this->assertEquals([], $configuration->getMenus());
    }
}

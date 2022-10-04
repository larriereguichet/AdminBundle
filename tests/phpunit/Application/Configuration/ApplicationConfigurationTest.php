<?php

namespace LAG\AdminBundle\Tests\Application\Configuration;

use JK\Configuration\Exception\InvalidConfigurationException;
use LAG\AdminBundle\Admin\Admin;
use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Field\ActionCollectionField;
use LAG\AdminBundle\Field\ActionField;
use LAG\AdminBundle\Field\ArrayField;
use LAG\AdminBundle\Field\AutoField;
use LAG\AdminBundle\Field\BooleanField;
use LAG\AdminBundle\Field\CountField;
use LAG\AdminBundle\Field\DateField;
use LAG\AdminBundle\Field\LinkField;
use LAG\AdminBundle\Field\MappedField;
use LAG\AdminBundle\Field\StringField;
use LAG\AdminBundle\Metadata\Operation;
use LAG\AdminBundle\Tests\TestCase;

class ApplicationConfigurationTest extends TestCase
{
    public function testService(): void
    {
        $this->assertServiceExists(ApplicationConfiguration::class);
        $this->assertServiceExists('lag_admin.application');
    }

    public function testDefaultConfiguration(): void
    {
        $configuration = new ApplicationConfiguration([
            'resources_path' => 'test/',
        ]);

        $this->assertEquals([
            'title' => 'Admin Application',
            'description' => 'Admin Application',
            'admin_class' => Admin::class,
            'action_class' => Operation::class,
            'base_template' => '@LAGAdmin/base.html.twig',
            'ajax_template' => '@LAGAdmin/empty.html.twig',
            'create_template' => '@LAGAdmin/crud/create.html.twig',
            'update_template' => '@LAGAdmin/crud/update.html.twig',
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
                'domain' => 'admin',
            ],
            'fields_mapping' => [
                'string' => StringField::class,
                'text' => StringField::class,
                'float' => StringField::class,
                'integer' => StringField::class,
                'array' => ArrayField::class,
                'action' => ActionField::class,
                'boolean' => BooleanField::class,
                'mapped' => MappedField::class,
                'action_collection' => ActionCollectionField::class,
                'link' => LinkField::class,
                'date' => DateField::class,
                'count' => CountField::class,
                'auto' => AutoField::class,
            ],
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
        $this->assertEquals(Admin::class, $configuration->getAdminClass());
        $this->assertEquals(Operation::class, $configuration->getActionClass());

        $this->assertEquals('@LAGAdmin/base.html.twig', $configuration->getBaseTemplate());
        $this->assertEquals('@LAGAdmin/empty.html.twig', $configuration->getAjaxTemplate());
        $this->assertEquals('@LAGAdmin/crud/create.html.twig', $configuration->getCreateTemplate());
        $this->assertEquals('@LAGAdmin/crud/update.html.twig', $configuration->getUpdateTemplate());
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
        $this->assertEquals('admin', $configuration->getTranslationDomain());

        $this->assertEquals([
            'string' => StringField::class,
            'text' => StringField::class,
            'float' => StringField::class,
            'integer' => StringField::class,
            'array' => ArrayField::class,
            'action' => ActionField::class,
            'boolean' => BooleanField::class,
            'mapped' => MappedField::class,
            'action_collection' => ActionCollectionField::class,
            'link' => LinkField::class,
            'date' => DateField::class,
            'count' => CountField::class,
            'auto' => AutoField::class,
        ], $configuration->getFieldsMapping());

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
        $configuration->getTranslationDomain();
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
}

<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Admin\Configuration;

use LAG\AdminBundle\Admin\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Field\Field;
use LAG\AdminBundle\Tests\Base;

class ActionConfigurationTest extends Base
{
    public function testActionConfiguration()
    {
        $configuration = new ApplicationConfiguration([
            'enable_extra_configuration' => true,
            'title' => 'My Title',
            'description' => 'Test',
            'locale' => 'fr',
            'layout' => 'LAGAdminBundle::admin.layout.html.twig',
            'block_template' => 'LAGAdminBundle:Form:fields.html.twig',
            'bootstrap' => true,
            'date_format' => 'd/m/YYYY',
            'string_length' => 100,
            'string_length_truncate' => '....',
            'routing' => [
                'url_pattern' => '/{admin}/{action}',
                'name_pattern' => 'lag.admin.{admin}',
            ],
            'translation' => [
                'enabled' => true,
                'pattern' => 'lag.admin.{key}',
            ],
            'max_per_page' => 25,
            'fields_mapping' => [
                'custom' => 'custom',
                Field::TYPE_STRING => 'LAG\AdminBundle\Field\Field\StringField',
                Field::TYPE_ARRAY => 'LAG\AdminBundle\Field\Field\ArrayField',
                Field::TYPE_LINK => 'LAG\AdminBundle\Field\Field\Link',
                Field::TYPE_DATE => 'LAG\AdminBundle\Field\Field\Date',
                Field::TYPE_COUNT => 'LAG\AdminBundle\Field\Field\Count',
                Field::TYPE_ACTION => 'LAG\AdminBundle\Field\Field\Action',
                Field::TYPE_COLLECTION => 'LAG\AdminBundle\Field\Field\Collection',
                Field::TYPE_BOOLEAN => 'LAG\AdminBundle\Field\Field\Boolean',
            ],
        ], 'en');

        $this->assertEquals(true, $configuration->isExtraConfigurationEnabled());
        $this->assertEquals('My Title', $configuration->getTitle());
        $this->assertEquals('Test', $configuration->getDescription());
        $this->assertEquals('fr', $configuration->getLocale());
        $this->assertEquals('LAGAdminBundle::admin.layout.html.twig', $configuration->getLayout());
        $this->assertEquals('LAGAdminBundle:Form:fields.html.twig', $configuration->getBlockTemplate());
        $this->assertEquals(true, $configuration->useBootstrap());
        $this->assertEquals(true, $configuration->isBootstrap());
        $this->assertEquals('d/m/YYYY', $configuration->getDateFormat());
        $this->assertEquals(100, $configuration->getStringLength());
        $this->assertEquals('....', $configuration->getStringLengthTruncate());
        $this->assertEquals('lag.admin.{admin}', $configuration->getRoutingNamePattern());
        $this->assertEquals('/{admin}/{action}', $configuration->getRoutingUrlPattern());
        $this->assertEquals('lag.admin.{key}', $configuration->getTranslationPattern());
        $this->assertEquals(25, $configuration->getMaxPerPage());
        $this->assertEquals([
            'custom' => 'custom',
            Field::TYPE_STRING => 'LAG\AdminBundle\Field\Field\StringField',
            Field::TYPE_ARRAY => 'LAG\AdminBundle\Field\Field\ArrayField',
            Field::TYPE_LINK => 'LAG\AdminBundle\Field\Field\Link',
            Field::TYPE_DATE => 'LAG\AdminBundle\Field\Field\Date',
            Field::TYPE_COUNT => 'LAG\AdminBundle\Field\Field\Count',
            Field::TYPE_ACTION => 'LAG\AdminBundle\Field\Field\Action',
            Field::TYPE_COLLECTION => 'LAG\AdminBundle\Field\Field\Collection',
            Field::TYPE_BOOLEAN => 'LAG\AdminBundle\Field\Field\Boolean',
        ], $configuration->getFieldsMapping());
    }
}

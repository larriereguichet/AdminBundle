<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Application\Configuration;

use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Field\Field;
use LAG\AdminBundle\Tests\Base;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ApplicationConfigurationTest extends Base
{
    /**
     * Application configuration SHOULD return configured values.
     */
    public function testConfigureOptions()
    {
        $resolver = new OptionsResolver();

        $applicationConfiguration = new ApplicationConfiguration($this->mockKernel());
        $applicationConfiguration->configureOptions($resolver);
        $parameters = $resolver->resolve([
            'enable_extra_configuration' => true,
            'title' => 'My Title',
            'description' => 'Test',
            'locale' => 'fr',
            'base_template' => 'LAGAdminBundle::admin.layout.html.twig',
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
            ],
        ]);
        $applicationConfiguration->setParameters($parameters);

        $this->assertEquals(true, $applicationConfiguration->getParameter('enable_extra_configuration'));
        $this->assertEquals('My Title', $applicationConfiguration->getParameter('title'));
        $this->assertEquals('Test', $applicationConfiguration->getParameter('description'));
        $this->assertEquals('fr', $applicationConfiguration->getParameter('locale'));
        $this->assertEquals('LAGAdminBundle::admin.layout.html.twig', $applicationConfiguration->getParameter('base_template'));
        $this->assertEquals('LAGAdminBundle:Form:fields.html.twig', $applicationConfiguration->getParameter('block_template'));
        $this->assertEquals(true, $applicationConfiguration->getParameter('bootstrap'));
        $this->assertEquals('d/m/YYYY', $applicationConfiguration->getParameter('date_format'));
        $this->assertEquals(100, $applicationConfiguration->getParameter('string_length'));
        $this->assertEquals('....', $applicationConfiguration->getParameter('string_length_truncate'));
        $this->assertEquals('lag.admin.{admin}', $applicationConfiguration->getParameter('routing')['name_pattern']);
        $this->assertEquals('/{admin}/{action}', $applicationConfiguration->getParameter('routing')['url_pattern']);
        $this->assertEquals('lag.admin.{key}', $applicationConfiguration->getParameter('translation')['pattern']);
        $this->assertEquals(25, $applicationConfiguration->getParameter('max_per_page'));
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
        ], $applicationConfiguration->getParameter('fields_mapping'));
    }
}

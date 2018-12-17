<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Admin\Configuration;

use LAG\AdminBundle\Admin\Configuration\AdminConfiguration;
use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Tests\AdminTestBase;
use LAG\AdminBundle\Tests\Entity\TestEntity;
use LAG\AdminBundle\Tests\Form\TestForm;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminConfigurationTest extends AdminTestBase
{
    public function testConfiguration()
    {
        $resolver = new OptionsResolver();

        $applicationConfiguration = new ApplicationConfiguration();
        $applicationConfiguration->configureOptions($resolver);
        $applicationParameters = $resolver->resolve([
            'routing' => [
                'url_pattern' => '/test/{admin}/{action}',
                'name_pattern' => 'test.{admin}.{action}',
            ],
            'translation' => [
                'enabled' => true,
                'pattern' => '{key}',
            ],
            'max_per_page' => 666,
        ]);
        $applicationConfiguration->setParameters($applicationParameters);

        $resolver->clear();
        $adminConfiguration = new AdminConfiguration($applicationConfiguration);
        $adminConfiguration->configureOptions($resolver);
        $parameters = $resolver->resolve([
            'entity' => TestEntity::class,
            'form' => TestForm::class,
            'actions' => [
                'list' => [],
                'edit' => null,
            ],
        ]);
        // must return a array of parameters
        $this->assertInternalType('array', $parameters);

        // class and form should be unchanged
        $this->assertEquals(TestEntity::class, $parameters['entity']);
        $this->assertEquals(TestForm::class, $parameters['form']);

        // actions should be present
        $this->assertArrayHasKey('list', $parameters['actions']);
        $this->assertArrayHasKey('edit', $parameters['actions']);
        $this->assertArrayHasKey('batch', $parameters['actions']);

        // application configuration pattern should be set
        $this->assertEquals('/test/{admin}/{action}', $parameters['routing_url_pattern']);
        $this->assertEquals('test.{admin}.{action}', $parameters['routing_name_pattern']);

        // application translation pattern should be set
        $this->assertEquals('{key}', $parameters['translation_pattern']);

        // application page should be set
        $this->assertEquals(666, $parameters['max_per_page']);
    }
}

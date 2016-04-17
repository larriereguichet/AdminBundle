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

        $applicationConfiguration = new ApplicationConfiguration($this->mockKernel());
        $applicationConfiguration->configureOptions($resolver);
        $applicationParameters = $resolver->resolve([]);
        $applicationConfiguration->setParameters($applicationParameters);

        $resolver->clear();
        $adminConfiguration = new AdminConfiguration($applicationConfiguration);
        $adminConfiguration->configureOptions($resolver);
        $parameters = $resolver->resolve([
            'entity' => TestEntity::class,
            'form' => TestForm::class
        ]);
        $adminConfiguration->setParameters($parameters);

        $this->assertEquals(TestEntity::class, $adminConfiguration->getParameter('entity'));
        $this->assertEquals(TestForm::class, $adminConfiguration->getParameter('form'));
    }
}

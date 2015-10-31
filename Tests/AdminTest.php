<?php

namespace LAG\AdminBundle\Tests;

use LAG\AdminBundle\Admin\Admin;
use LAG\AdminBundle\Admin\Configuration\AdminConfiguration;
use Test\TestBundle\Entity\TestEntity;

class AdminTest  extends Base
{
    public function testAdmin()
    {
        $repository = $this
            ->getMockBuilder('Doctrine\Common\Persistence\ObjectRepository')
            ->getMock();
        $manager = $this
            ->getMockBuilder('LAG\AdminBundle\Manager\GenericManager')
            ->disableOriginalConstructor()
            ->getMock();
        $configuration = [
            'controller' => 'LAGAdminBundle:Generic',
            'manager' => 'LAG\AdminBundle\Manager\GenericManager',
            'entity' => 'Test',
            'form' => 'test',
            'actions' => [
                'minimal_action' => [],
                'other_action' => [],
                'full_action' => [],
            ],
            'max_per_page' => 50,
            'routing_url_pattern' => 'lag.admin.{admin}',
            'routing_name_pattern' => 'lag.{admin}.{action}'
        ];
        $adminConfiguration = new AdminConfiguration($configuration);
        $admin = new Admin(
            'test_admin',
            $repository,
            $manager,
            $adminConfiguration
        );
        $this->assertEquals('test_admin', $admin->getName());
        $this->assertEquals($configuration['entity'], $admin->getEntityNamespace());
        $this->assertEquals($repository, $admin->getRepository());
        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $admin->getEntities());

        $entity = new TestEntity();
        $admin->setEntities([$entity]);
        $this->assertEquals([$entity], $admin->getEntities());
        $this->assertEquals($configuration['form'], $admin->getFormType());



    }
}

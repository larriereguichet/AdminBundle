<?php

namespace Admin\Factory;

use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Tests\Base;
use Test\TestBundle\Entity\TestEntity;

class AdminFactoryTest extends Base
{
    /**
     * Init method should create Admin object according to given configuration
     */
    public function testInit()
    {
        // admin factory should work without configuration
        $this->mockAdminFactory();
        // test admin creation
        $configuration = $this->getAdminsConfiguration();
        $adminFactory = $this->mockAdminFactory($configuration);
        $adminFactory->init();

        foreach ($configuration as $name => $adminConfiguration) {
            $admin = $adminFactory->getAdmin($name);
            $this->doTestAdmin($admin, $adminConfiguration, $name);
        }
    }

    protected function doTestAdmin(AdminInterface $admin, array $configuration, $adminName)
    {
        $entity = new TestEntity();
        $admin->setEntities([$entity]);
        $this->assertEquals([$entity], $admin->getEntities());
        $this->assertEquals($admin->getName(), $adminName);
        $this->assertEquals($admin->getFormType(), $configuration['form']);
        $this->assertEquals($admin->getEntityNamespace(), $configuration['entity']);

        if (array_key_exists('controller', $configuration)) {
            $this->assertEquals($admin->getController(), $configuration['controller']);
        }
        if (array_key_exists('max_per_page', $configuration)) {
            $this->assertEquals($admin->getConfiguration()->getMaxPerPage(), $configuration['max_per_page']);
        } else {
            $this->assertEquals($admin->getConfiguration()->getMaxPerPage(), 25);
        }
    }

    protected function getAdminsConfiguration()
    {
        return [
            'minimal_configuration' => [
                'entity' => 'Test\TestBundle\Entity\TestEntity',
                'form' => 'test'
            ],
            'full_entity' => [
                'entity' => 'Test\TestBundle\Entity\TestEntity',
                'form' => 'test',
                'controller' => 'TestTestBundle:Test',
                'max_per_page' => 50,
                'actions' => [
                    'custom_list' => [],
                    'custom_edit' => [],
                ],
                'manager' => 'Test\TestBundle\Manager\TestManager',
                'routing_url_pattern' => 'lag.admin.{admin}',
                'routing_name_pattern' => 'lag.{admin}.{action}'
            ]
        ];
    }
}

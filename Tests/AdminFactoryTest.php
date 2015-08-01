<?php

namespace BlueBear\AdminBundle\Tests;

use BlueBear\AdminBundle\Admin\AdminFactory;

class AdminFactoryTest extends Base
{
    public function testInitNoParameters()
    {
        $client = $this->initApplication();
        $container = $client->getKernel()->getContainer();
        // admin factory initialization
        $adminFactory = new AdminFactory($container);
        $this->assertTrue($adminFactory != null, 'AdminFactory initialization error');
        $this->assertCount(0, $adminFactory->getAdmins(), 'No admin should be found');
    }

    public function testCreateAdminFromConfig()
    {
        $client = $this->initApplication();
        $container = $client->getKernel()->getContainer();
        $adminFactory = new AdminFactory($container);
        $config = $this->getFakeAdminsConfiguration();

        foreach ($config as $adminName => $adminConfig) {
            $adminFactory->createAdminFromConfig($adminName, $adminConfig);
            $admin = $adminFactory->getAdmin($adminName);
            $this->assertInstanceOf('BlueBear\AdminBundle\Admin\Admin', $admin, 'Admin not found after creation');
        }
    }

    protected function getFakeAdminsConfiguration()
    {
        return [
            'MyTestEntity' => [
                'entity' => 'Test',
                'form' => 'test',
                'controller' => 'BlueBearAdminBundle:Generic'
            ]
        ];
    }
}

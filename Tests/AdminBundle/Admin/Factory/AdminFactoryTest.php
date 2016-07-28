<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Admin\Factory;

use Exception;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Tests\AdminTestBase;

class AdminFactoryTest extends AdminTestBase
{
    /**
     * Init method should create Admin object according to given configuration.
     */
    public function testInit()
    {
        // admin factory should work without configuration
        $this->createAdminFactory();
        // test admin creation
        $configuration = $this->getAdminsConfiguration();
        $adminFactory = $this->createAdminFactory($configuration);
        $adminFactory->init();

        foreach ($configuration as $name => $adminConfiguration) {
            $admin = $adminFactory
                ->getRegistry()
                ->get($name);
            $this->doTestAdmin($admin, $adminConfiguration, $name);
        }
    }

    /**
     * Create method should return an Admin according to given configuration.
     *
     * @throws Exception
     */
    public function testCreate()
    {
        // test minimal configuration
        $adminConfiguration = $this->getAdminsConfiguration()['minimal_configuration'];
        // mock admin factory with empty configuration
        $adminFactory = $this->createAdminFactory();
        $admin = $adminFactory->create('admin_test', $adminConfiguration);
        $this->doTestAdmin($admin, $adminConfiguration, 'admin_test');

        // test full configuration
        $adminConfiguration = $this->getAdminsConfiguration()['full_configuration'];
        $admin = $adminFactory->create('admin_test2', $adminConfiguration);
        $this->doTestAdmin($admin, $adminConfiguration, 'admin_test2');
    }

    /**
     * AddDataProvider method must add a DataProviderInterface to the Admin.
     */
    public function testAddDataProvider()
    {
        // test with no configuration
        $adminFactory = $this->createAdminFactory();
        // unknown admin not exists, it should throw an exception
        $this->assertExceptionRaised('Exception', function () use ($adminFactory) {
            $adminFactory
                ->getRegistry()
                ->get('unknown_admin');
        });
        $dataProvider = $this->mockDataProvider();
        $adminFactory->addDataProvider('test', $dataProvider);
    }

    /**
     * @param AdminInterface $admin
     * @param array $configuration
     * @param $adminName
     */
    protected function doTestAdmin(AdminInterface $admin, array $configuration, $adminName)
    {
        $this->assertEquals($admin->getName(), $adminName);

        foreach ($configuration as $key => $value) {
            $this->assertEquals($admin->getConfiguration()->getParameter($key), $configuration[$key]);
        }
    }
}

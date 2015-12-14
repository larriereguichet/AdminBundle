<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Admin\Factory;

use Exception;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Tests\Base;
use Symfony\Component\HttpFoundation\Request;

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

    /**
     * Create method should return an Admin according to given configuration
     *
     * @throws Exception
     */
    public function testCreate()
    {
        // test minimal configuration
        $adminConfiguration = $this->getAdminsConfiguration()['minimal_configuration'];
        // mock admin factory with empty configuration
        $adminFactory = $this->mockAdminFactory();
        $admin = $adminFactory->create('admin_test', $adminConfiguration);
        $this->doTestAdmin($admin, $adminConfiguration, 'admin_test');

        // test full configuration
        $adminConfiguration = $this->getAdminsConfiguration()['full_configuration'];
        $admin = $adminFactory->create('admin_test2', $adminConfiguration);
        $this->doTestAdmin($admin, $adminConfiguration, 'admin_test2');
    }

    /**
     * GetAdminFromRequest method should return a configured Admin from request parameters
     *
     * @throws Exception
     */
    public function testGetAdminFromRequest()
    {
        $adminConfiguration = $this->getAdminsConfiguration();
        $adminFactory = $this->mockAdminFactory($adminConfiguration);
        $adminFactory->init();

        foreach ($adminConfiguration as $name => $configuration) {
            $request = new Request([], [], [
                '_route_params' => [
                    '_admin' => $name,
                    // see Base->mockActionFactory
                    '_action' => 'test'
                ]
            ]);
            $admin = $adminFactory->getAdminFromRequest($request);
            $this->doTestAdmin($admin, $configuration, $name);
        }
    }

    /**
     * GetAdmin method should return an configured Admin by its name
     *
     * @throws Exception
     */
    public function testGetAdmin()
    {
        // test with no configuration
        $adminFactory = $this->mockAdminFactory();
        // unknow admin not exists, it should throw an exception
        $this->assertExceptionRaised('Exception', function () use ($adminFactory) {
            $adminFactory->getAdmin('unknown_admin');
        });
        // test with configurations samples
        $adminsConfiguration = $this->getAdminsConfiguration();
        $adminFactory = $this->mockAdminFactory($adminsConfiguration);
        $adminFactory->init();

        foreach ($adminsConfiguration as $name => $configuration) {
            $admin = $adminFactory->getAdmin($name);
            $this->doTestAdmin($admin, $configuration, $name);
        }
    }

    /**
     * @param AdminInterface $admin
     * @param array $configuration
     * @param $adminName
     */
    protected function doTestAdmin(AdminInterface $admin, array $configuration, $adminName)
    {
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
}

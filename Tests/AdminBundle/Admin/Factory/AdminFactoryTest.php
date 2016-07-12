<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Admin\Factory;

use Exception;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Admin\Event\AdminEvent;
use LAG\AdminBundle\Admin\Event\AdminEvents;
use LAG\AdminBundle\Tests\AdminTestBase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;

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
            $admin = $adminFactory->getAdmin($name);
            $this->doTestAdmin($admin, $adminConfiguration, $name);
        }
        // the second initialization should work too
        $adminFactory->init();
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
     * GetAdminFromRequest method should return a configured Admin from request parameters.
     *
     * @throws Exception
     */
    public function testGetAdminFromRequest()
    {
        $adminConfiguration = $this->getAdminsConfiguration();
        $adminFactory = $this->createAdminFactory($adminConfiguration);
        $adminFactory->init();

        foreach ($adminConfiguration as $name => $configuration) {
            $request = new Request([], [], [
                '_route_params' => [
                    '_admin' => $name,
                    // see AdminTestBase->mockActionFactory
                    '_action' => 'test'
                ]
            ]);
            $admin = $adminFactory->getAdminFromRequest($request);
            $this->doTestAdmin($admin, $configuration, $name);
        }
    }

    /**
     * GetAdmin method should return an configured Admin by its name.
     *
     * @throws Exception
     */
    public function testGetAdmin()
    {
        // test with no configuration
        $adminFactory = $this->createAdminFactory();

        // unknown admin not exists, it should throw an exception
        $this->assertExceptionRaised('Exception', function () use ($adminFactory) {
            $adminFactory->getAdmin('unknown_admin');
        });

        // test with configurations samples
        $adminsConfiguration = $this->getAdminsConfiguration();
        $adminFactory = $this->createAdminFactory($adminsConfiguration);
        $adminFactory->init();

        foreach ($adminsConfiguration as $name => $configuration) {
            $admin = $adminFactory->getAdmin($name);
            $this->doTestAdmin($admin, $configuration, $name);
        }
    }

    /**
     * GetAdmins method should return all configured Admin.
     *
     * @throws Exception
     */
    public function testGetAdmins()
    {
        // test with no configuration
        $adminFactory = $this->createAdminFactory();
        // unknow admin not exists, it should throw an exception
        $this->assertExceptionRaised('Exception', function () use ($adminFactory) {
            $adminFactory->getAdmin('unknown_admin');
        });
        // test with configurations samples
        $adminsConfiguration = $this->getAdminsConfiguration();
        $adminFactory = $this->createAdminFactory($adminsConfiguration);
        $adminFactory->init();

        $admins = $adminFactory->getAdmins();

        foreach ($admins as $admin) {
            $this->assertArrayHasKey($admin->getName(), $adminsConfiguration);
            $this->doTestAdmin($admin, $adminsConfiguration[$admin->getName()], $admin->getName());
        }
    }

    /**
     * AddDataProvider method must add a DataProviderInterface to the Admin.
     */
    public function testAddDataProvider()
    {
        // test with no configuration
        $adminFactory = $this->createAdminFactory();
        // unknow admin not exists, it should throw an exception
        $this->assertExceptionRaised('Exception', function () use ($adminFactory) {
            $adminFactory->getAdmin('unknown_admin');
        });
        $dataProvider = $this->mockDataProvider();
        $adminFactory->addDataProvider('test', $dataProvider);
    }

    /**
     * Test dispatched events.
     */
    public function testEvents()
    {
        $isConfigurationEventCalled = false;
        $adminCreateCount = 0;

        $eventDispatcher = new EventDispatcher();
        $adminFactory = $this->createAdminFactory([], $eventDispatcher);

        // before configuration event SHOULD be dispatched
        $eventDispatcher->addListener(
            AdminEvents::BEFORE_CONFIGURATION,
            function (AdminEvent $event) use (&$isConfigurationEventCalled) {
                $isConfigurationEventCalled = true;
                $this->assertInternalType('array', $event->getAdminsConfiguration());
            }
        );

        // admin create event SHOULD be dispatched
        $eventDispatcher->addListener(
            AdminEvents::ADMIN_CREATE,
            function (AdminEvent $event) use (&$adminCreateCount, $adminFactory) {
                $adminCreateCount++;
                $this->assertInternalType('array', $event->getAdminsConfiguration());
                $this->assertEquals($adminFactory->getAdmin($event->getAdminName()), $event->getAdminConfiguration());
            }
        );

        // admin after create event SHOULD be dispatched
        $eventDispatcher->addListener(
            AdminEvents::ADMIN_CREATED,
            function (AdminEvent $event) {
                $this->assertInstanceOf(AdminInterface::class, $event->getAdmin());
            }
        );

        $adminFactory->init();

        $this->assertTrue($isConfigurationEventCalled);
        $this->assertEquals(count($adminFactory->getAdmins()), $adminCreateCount);
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

<?php

namespace LAG\AdminBundle\Tests;

use Closure;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Exception;
use LAG\AdminBundle\Admin\ActionInterface;
use LAG\AdminBundle\Admin\Admin;
use LAG\AdminBundle\Admin\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Admin\Factory\ActionFactory;
use LAG\AdminBundle\Admin\Factory\AdminFactory;
use LAG\AdminBundle\Admin\ManagerInterface;
use LAG\AdminBundle\Admin\Message\MessageHandler;
use PHPUnit_Framework_MockObject_MockObject;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class Base extends WebTestCase
{
    protected static $isDatabaseCreated = false;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var Client
     */
    protected $client;

    /**
     * Initialize an application with a container and a client. Create database if required.
     *
     * @param string $url
     * @param null $method
     * @param array $parameters
     */
    public function initApplication($url = '/', $method = null, $parameters = [])
    {
        // creating kernel client
        $this->client = Client::initClient();
        // test client initialization
        $this->assertTrue($this->client != null, 'TestClient successfully initialized');

        // initialise database
        if (!self::$isDatabaseCreated) {
            // TODO remove database at the end of the tests
            exec(__DIR__ . '/app/console doctrine:database:create --if-not-exists', $output);
            exec(__DIR__ . '/app/console doctrine:schema:update --force', $output);

            foreach ($output as $line) {
                // TODO only in verbose mode
                fwrite(STDOUT, $line . "\n");
            }
            fwrite(STDOUT, "\n");
            self::$isDatabaseCreated = true;
        }
        // init a request
        $request = Request::create($url, $method, $parameters);
        // do request
        $this->client->doRequest($request);
        $this->container = $this->client->getContainer();
    }

    /**
     * Assert that an exception is raised in the given code.
     *
     * @param $exceptionClass
     * @param Closure $closure
     */
    protected function assertExceptionRaised($exceptionClass, Closure $closure)
    {
        $e = null;
        $isClassValid = false;

        try {
            $closure();
        } catch (Exception $e) {
            if (get_class($e) == $exceptionClass) {
                $isClassValid = true;
            }
        }
        $this->assertTrue($isClassValid, 'Expected ' . $exceptionClass . ', got ' . get_class($e));
    }

    protected function logIn($login = 'admin', $roles = null)
    {
        /*$session = $this
            ->container
            ->get('session');

        if ($roles === null) {
            $roles = [
                'ROLE_ADMIN',
                'ROLE_USER',
            ];
        }
        $firewall = 'secured_area';
        //$token = new UsernamePasswordToken($login, null, $firewall, $roles);
        //$session->set('_security_'.$firewall, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);*/
    }

    protected function mockAdmin($name, $configuration)
    {
        return new Admin(
            $name,
            $this->mockEntityRepository(),
            $this->mockManager(),
            $configuration,
            $this->mockMessageHandler()
        );
    }

    /**
     * @param $name
     * @return ActionInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockAction($name)
    {
        $action = $this
            ->getMockBuilder('LAG\AdminBundle\Admin\ActionInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $action
            ->method('getName')
            ->willReturn($name);

        return $action;
    }

    protected function mockAdminFactory(array $configuration = [])
    {
        /** @var EventDispatcher $mockEventDispatcher */
        $mockEventDispatcher = $this
            ->getMockBuilder('Symfony\Component\EventDispatcher\EventDispatcher')
            ->getMock();

        return new AdminFactory(
            $mockEventDispatcher,
            $this->mockEntityManager(),
            $this->mockApplicationConfiguration(),
            $configuration,
            $this->mockActionFactory(),
            $this->mockMessageHandler()
        );
    }

    /**
     * @return ManagerInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockManager()
    {
        $managerMock = $this
            ->getMockBuilder('LAG\AdminBundle\Admin\ManagerInterface')
            ->getMock();
        $managerMock
            ->method('getRepository')
            ->willReturn('Doctrine\ORM\EntityRepository');

        return $managerMock;
    }

    /**
     * @return Session|PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockSession()
    {
        if ($this->container) {
            $session = $this
                ->container
                ->get('session');
        } else {
            $session = $this
                ->getMockBuilder('Symfony\Component\HttpFoundation\Session\Session')
                ->disableOriginalConstructor()
                ->getMock();
        }
        return $session;
    }

    /**
     * @return Logger|PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockLogger()
    {
        if ($this->container) {
            $logger = $this
                ->container
                ->get('logger');
        } else {
            $logger = $this
                ->getMockBuilder('Monolog\Logger')
                ->disableOriginalConstructor()
                ->getMock();
        }
        return $logger;
    }

    /**
     * @return EntityRepository|PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockEntityRepository()
    {
        return $this
            ->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return EntityManager|PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockEntityManager()
    {
        $entityManager = $this
            ->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();
        $repository = $this->mockEntityRepository();
        $entityManager
            ->method('getRepository')
            ->willReturn($repository);

        return $entityManager;
    }

    /**
     * @return ActionFactory|PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockActionFactory()
    {
        $actionFactory = $this
            ->getMockBuilder('LAG\AdminBundle\Admin\Factory\ActionFactory')
            ->disableOriginalConstructor()
            ->getMock();
        $actionFactory
            ->method('create')
            ->willReturn($this->mockAction('test'));

        return $actionFactory;
    }

    /**
     * @return TokenStorageInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockTokenStorage()
    {
        return $this
            ->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface')
            ->getMock();
    }

    /**
     * @return ApplicationConfiguration
     */
    protected function mockApplicationConfiguration()
    {
        $applicationConfiguration = $this
            ->getMockBuilder('LAG\AdminBundle\Admin\Configuration\ApplicationConfiguration')
            ->disableOriginalConstructor()
            ->getMock();
        $applicationConfiguration
            ->method('getMaxPerPage')
            ->willReturn(25);

        return $applicationConfiguration;
    }

    /**
     * @return MessageHandler|PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockMessageHandler()
    {
        $messageHandler = $this
            ->getMockBuilder('LAG\AdminBundle\Admin\Message\MessageHandler')
            ->disableOriginalConstructor()
            ->getMock();

        return $messageHandler;
    }
}

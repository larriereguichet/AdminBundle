<?php

namespace LAG\AdminBundle\Tests;

use Closure;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Exception;
use Knp\Menu\MenuFactory;
use LAG\AdminBundle\Action\ActionInterface;
use LAG\AdminBundle\Admin\Admin;
use LAG\AdminBundle\Action\Configuration\ActionConfiguration;
use LAG\AdminBundle\Admin\Configuration\AdminConfiguration;
use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Action\Factory\ActionFactory;
use LAG\AdminBundle\DataProvider\DataProviderInterface;
use LAG\AdminBundle\Field\Factory\FieldFactory;
use LAG\AdminBundle\Message\MessageHandlerInterface;
use LAG\AdminBundle\Repository\RepositoryInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use ReflectionClass;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Translation\TranslatorInterface;

class AdminTestBaseOLD extends TestCase
{
    /**
     * @var bool
     */
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
        $message = '';

        try {
            $closure();
        } catch (Exception $e) {
            if (get_class($e) == $exceptionClass) {
                $isClassValid = true;
            }
            $message = $e->getMessage();
        }
        $this->assertNotNull($e, 'No Exception was thrown');
        $this->assertTrue($isClassValid, sprintf('Expected %s, got %s (Exception message : "%s")',
            $exceptionClass,
            get_class($e),
            $message
        ));
    }

    /**
     * @param ApplicationConfiguration $applicationConfiguration
     * @param array $configuration
     * @return AdminConfiguration
     */
    protected function createAdminConfiguration(ApplicationConfiguration $applicationConfiguration, array $configuration = [])
    {
        $resolver = new OptionsResolver();
        $adminConfiguration = new AdminConfiguration($applicationConfiguration);
        $adminConfiguration->configureOptions($resolver);
        $adminConfiguration->setParameters($resolver->resolve($configuration));

        return $adminConfiguration;
    }

    /**
     * @return MenuFactory
     */
    protected function createKnpMenuFactory()
    {
        return new MenuFactory();
    }
    
    /**
     * Return Admin configurations samples
     *
     * @return array
     */
    protected function getAdminsConfiguration()
    {
        return [
            'minimal_configuration' => [
                'entity' => 'Test\TestBundle\Entity\TestEntity',
                'form' => 'test'
            ],
            'full_configuration' => [
                'entity' => 'Test\TestBundle\Entity\TestEntity',
                'form' => 'test',
                'controller' => 'TestTestBundle:Test',
                'max_per_page' => 50,
                'actions' => [
                    'custom_list' => [],
                    'custom_edit' => [],
                ],
                'routing_url_pattern' => 'lag.admin.{admin}',
                'routing_name_pattern' => 'lag.{admin}.{action}'
            ]
        ];
    }

    /**
     * @return KernelInterface | PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockKernel()
    {
        return $this
            ->getMockBuilder(KernelInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @param $name
     * @return ActionInterface | PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockAction($name)
    {
        $action = $this
            ->getMockBuilder(ActionInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $action
            ->method('getName')
            ->willReturn($name);

        return $action;
    }

    /**
     * @return ActionConfiguration | PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockActionConfiguration()
    {
        $configuration = $this
            ->getMockBuilder('LAG\AdminBundle\Admin\Configuration\ActionConfiguration')
            ->disableOriginalConstructor()
            ->getMock();
        $configuration
            ->method('getLoadMethod')
            ->willReturn(Admin::LOAD_STRATEGY_MULTIPLE);
        $configuration
            ->method('getCriteria')
            ->willReturn([]);

        return $configuration;
    }

    /**
     * @return Session | PHPUnit_Framework_MockObject_MockObject
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
     * @return Logger | PHPUnit_Framework_MockObject_MockObject
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
     * Return a mock of an entity repository
     *
     * @return RepositoryInterface | PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockEntityRepository()
    {
        return $this
            ->getMockBuilder(RepositoryInterface::class)
            ->getMock();
    }

    /**
     * @return EntityManager | PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockEntityManager()
    {
        /** @var EntityManagerInterface | PHPUnit_Framework_MockObject_MockObject $entityManager */
        $entityManager = $this
            ->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();
        $repository = $this->mockEntityRepository();

        $entityManager
            ->method('getRepository')
            ->willReturn($repository);
        $entityManager
            ->method('getClassMetadata')
            ->willReturn(new ClassMetadata('LAG\AdminBundle\Tests\Entity\TestEntity'));

        return $entityManager;
    }

    /**
     * @return Registry | PHPUnit_Framework_MockObject_MockObject
     *
     * @deprecated
     */
    protected function mockDoctrine()
    {
        $doctrine = $this
            ->getMockBuilder(Registry::class)
            ->disableOriginalConstructor()
            ->getMock();
        $doctrine
            ->method('getEntityManager')
            ->willReturn($this->mockEntityManager());

        return $doctrine;
    }

    /**
     * @return ActionFactory | PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockActionFactory()
    {
        $actionFactory = $this
            ->getMockBuilder(ActionFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $actionFactory
            ->method('create')
            ->willReturn($this->mockAction('test'));

        return $actionFactory;
    }

    /**
     * @return TokenStorageInterface | PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockTokenStorage()
    {
        return $this
            ->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface')
            ->getMock();
    }

    /**
     * @return ApplicationConfiguration|PHPUnit_Framework_MockObject_MockObject
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
     * @return MessageHandlerInterface | PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockMessageHandler()
    {
        $messageHandler = $this
            ->getMockBuilder('LAG\AdminBundle\Message\MessageHandler')
            ->disableOriginalConstructor()
            ->getMock();

        return $messageHandler;
    }

    /**
     * @return TranslatorInterface | PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockTranslator()
    {
        return $this
            ->getMockBuilder('Symfony\Component\Translation\TranslatorInterface')
            ->getMock();
    }

    /**
     * @param array $entities
     * @return DataProviderInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockDataProvider($entities = [])
    {
        $dataProvider = $this
            ->getMockBuilder('LAG\AdminBundle\DataProvider\DataProviderInterface')
            ->getMock();
        $dataProvider
            ->method('findBy')
            ->willReturn($entities);

        return $dataProvider;
    }

    /**
     * @return FieldFactory|PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockFieldFactory()
    {
        $fieldFactory = $this
            ->getMockBuilder(FieldFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        return $fieldFactory;
    }
    
    /**
     * @param $class
     *
     * @return PHPUnit_Framework_MockObject_MockObject|mixed
     */
    protected function getMockWithoutConstructor($class)
    {
        return $this
            ->getMockBuilder($class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }
    
    protected function setPrivateProperty($object, $property, $value)
    {
        $reflection = new ReflectionClass($object);
        
        $property = $reflection->getProperty($property);
        $property->setAccessible(true);
        $property->setValue($object, $value);
    }
    
    protected function getPrivateProperty($object, $property)
    {
        $reflection = new ReflectionClass($object);
        
        $property = $reflection->getProperty($property);
        $property->setAccessible(true);
        
        return $property->getValue($object);
    }
}

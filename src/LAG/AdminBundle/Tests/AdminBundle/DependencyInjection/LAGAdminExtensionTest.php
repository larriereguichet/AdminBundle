<?php

namespace LAG\AdminBundle\Tests\AdminBundle\DependencyInjection;

use Knp\Bundle\MenuBundle\DependencyInjection\KnpMenuExtension;
use LAG\AdminBundle\Action\Factory\ActionFactory;
use LAG\AdminBundle\Configuration\Factory\ConfigurationFactory;
use LAG\AdminBundle\DependencyInjection\LAGAdminExtension;
use LAG\AdminBundle\Doctrine\Repository\DoctrineRepositoryFactory;
use LAG\AdminBundle\Field\Factory\FieldFactory;
use LAG\AdminBundle\Menu\Factory\MenuFactory;
use LAG\AdminBundle\Tests\AdminTestBase;
use LAG\AdminBundle\Tests\Utils\FakeEntityManager;
use Monolog\Logger;
use stdClass;
use Symfony\Bundle\FrameworkBundle\DependencyInjection\FrameworkExtension;
use Symfony\Bundle\FrameworkBundle\Templating\TemplateNameParser;
use Symfony\Bundle\TwigBundle\DependencyInjection\TwigExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Twig_Loader_Array;

class LAGAdminExtensionTest extends AdminTestBase
{
    /**
     * The load should allow the container to compile without errors.
     */
    public function testLoad()
    {
        $container = $this->getWorkingContainer();
        $extension = new LAGAdminExtension();
        $extension->load([
            'lag_admin' => [
                'application' => [],
            ]
        ], $container);

        $eventDispatcherExtension = new FrameworkExtension();
        $eventDispatcherExtension->load([], $container);

        $twigExtension = new TwigExtension();
        $twigExtension->load([], $container);

        $knpMenuExtension = new KnpMenuExtension();
        $knpMenuExtension->load([], $container);

        // the container should compile without errors
        $container->compile();
        $this->assertTrue(true);

        $this->assertServices($container);
    }

    /**
     * Load method should not throw an exception if no application section was found.
     */
    public function testLoadWithoutApplication()
    {
        $container = new ContainerBuilder();
        $extension = new LAGAdminExtension();
    
        $extension->load([], $container);
        // no exception raised
        $this->assertTrue(true);
    }

    /**
     * GetAlias method should return "lag_admin".
     */
    public function testGetAlias()
    {
        $container = new ContainerBuilder();
        $extension = new LAGAdminExtension();
        $extension->load([
            'lag_admin' => [
                'application' => []
            ]
        ], $container);

        $this->assertEquals('lag_admin', $extension->getAlias());
    }

    protected function assertServices(ContainerBuilder $container)
    {
        // assert factories are rightly instanciate
        $this->assertInstanceOf(ConfigurationFactory::class, $container->get('lag.admin.configuration_factory'));
        $this->assertInstanceOf(ActionFactory::class, $container->get('lag.admin.action_factory'));
        $this->assertInstanceOf(FieldFactory::class, $container->get('lag.admin.field_factory'));
        $this->assertInstanceOf(MenuFactory::class, $container->get('lag.admin.menu_factory'));
        $this->assertInstanceOf(DoctrineRepositoryFactory::class, $container->get('lag.admin.repository_factory'));
    }

    /**
     * Return a working container builder to compile.
     *
     * @return ContainerBuilder
     */
    protected function getWorkingContainer()
    {
        $kernel = $this
            ->getMockBuilder(KernelInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $generic = new Definition(stdClass::class);
        $fileLocator = new Definition(FileLocator::class);
        
        $templateNameParser = new Definition(TemplateNameParser::class, [
            $kernel,
        ]);
        
        $logger = new Definition(Logger::class, [
            'default'
        ]);
        $session= new Definition(Session::class);
        $twigLoader = new Definition(Twig_Loader_Array::class);

        $entityManager = new Definition();
        $entityManager->setClass(FakeEntityManager::class);
    
        $authorizationChecker = new Definition();
        $authorizationChecker->setClass(AuthorizationChecker::class);
        
        $tokenStorage = new Definition();
        $tokenStorage->setClass(TokenStorage::class);
    
        $formFactory = new Definition(FormFactoryInterface::class);
    
        $router = new Definition(RouterInterface::class);

        $container = new ContainerBuilder();
        $container->setParameter('kernel.debug', false);
        $container->setParameter('kernel.cache_dir', sys_get_temp_dir().'/AdminBundleTests/cache');
        $container->setParameter('kernel.root_dir', realpath(__DIR__.'/../../AdminBundle'));
        $container->setParameter('kernel.charset', 'utf8');
        $container->setParameter('kernel.secret', 'MyLittleSecret');
        $container->setParameter('kernel.bundles', []);
        $container->setParameter('kernel.bundles_metadata', []);
        $container->setParameter('kernel.environment', 'prod');

        $container->setDefinitions([
            'doctrine.orm.entity_manager' => $entityManager,
            'doctrine' => $generic,
            'router' => $router,
            'logger' => $logger,
            'session' => $session,
            'form.factory' => $formFactory,
            'templating.locator' => $fileLocator,
            'templating.name_parser' => $templateNameParser,
            'twig.loader' => $twigLoader,
            'security.authorization_checker' => $authorizationChecker,
            'security.token_storage' => $tokenStorage,
        ]);

        return $container;
    }
}

<?php

namespace LAG\AdminBundle\Tests\AdminBundle\DependencyInjection;

use Knp\Bundle\MenuBundle\DependencyInjection\KnpMenuExtension;
use LAG\AdminBundle\Action\Factory\ActionFactory;
use LAG\AdminBundle\Admin\Factory\AdminFactory;
use LAG\AdminBundle\Admin\Factory\FilterFactory;
use LAG\AdminBundle\Configuration\Factory\ConfigurationFactory;
use LAG\AdminBundle\DataProvider\Factory\DataProviderFactory;
use LAG\AdminBundle\DependencyInjection\LAGAdminExtension;
use LAG\AdminBundle\Field\Factory\FieldFactory;
use LAG\AdminBundle\Filter\Factory\RequestFilterFactory;
use LAG\AdminBundle\Menu\Factory\MenuFactory;
use LAG\AdminBundle\Tests\AdminTestBase;
use LAG\AdminBundle\Tests\Utils\FakeEntityManager;
use Monolog\Logger;
use stdClass;
use Symfony\Bundle\FrameworkBundle\DependencyInjection\FrameworkExtension;
use Symfony\Bundle\FrameworkBundle\Templating\TemplateNameParser;
use Symfony\Bundle\TwigBundle\DependencyInjection\TwigExtension;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\KernelInterface;

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
                'application' => []
            ]
        ], $container);
        $this->assertCount(28, $container->getDefinitions());

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
     * Load method should throw an exception if no application section was found.
     */
    public function testLoadWithoutApplication()
    {
        $container = new ContainerBuilder();
        $extension = new LAGAdminExtension();

        $this->assertExceptionRaised(InvalidConfigurationException::class, function () use ($extension, $container) {
            $extension->load([], $container);
        });
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
        $this->assertInstanceOf(AdminFactory::class, $container->get('lag.admin.factory'));
        $this->assertInstanceOf(ActionFactory::class, $container->get('lag.admin.action_factory'));
        $this->assertInstanceOf(FieldFactory::class, $container->get('lag.admin.field_factory'));
        $this->assertInstanceOf(FilterFactory::class, $container->get('lag.admin.filter_factory'));
        $this->assertInstanceOf(MenuFactory::class, $container->get('lag.admin.menu_factory'));
        $this->assertInstanceOf(RequestFilterFactory::class, $container->get('lag.admin.request_filter_factory'));
        $this->assertInstanceOf(DataProviderFactory::class, $container->get('lag.admin.data_providers_factory'));
    }

    /**
     * Return a working container builder to compile.
     *
     * @return ContainerBuilder
     */
    protected function getWorkingContainer()
    {
        $generic = new Definition();
        $generic->setClass(stdClass::class);
        $fileLocator = new Definition();
        $fileLocator->setClass(FileLocator::class);
        $templateNameParser = new Definition();
        $templateNameParser->setClass(TemplateNameParser::class);
        $templateNameParser->addArgument($this->createMock(KernelInterface::class));
        $logger = new Definition();
        $logger->setClass(Logger::class);
        $logger->addArgument('default');
        $session= new Definition();
        $session->setClass(Session::class);

        $entityManager = new Definition();
        $entityManager->setClass(FakeEntityManager::class);

        $container = new ContainerBuilder();
        $container->setParameter('kernel.debug', false);
        $container->setParameter('kernel.cache_dir', sys_get_temp_dir().'/AdminBundleTests/cache');
        $container->setParameter('kernel.root_dir', realpath(__DIR__.'/../../..'));
        $container->setParameter('kernel.charset', 'utf8');
        $container->setParameter('kernel.secret', 'MyLittleSecret');
        $container->setParameter('kernel.bundles', []);
        $container->setParameter('kernel.environment', 'prod');

        $container->setDefinitions([
            'doctrine.orm.entity_manager' => $entityManager,
            'doctrine' => $generic,
            'router' => $generic,
            'logger' => $logger,
            'session' => $session,
            'form.factory' => $generic,
            'templating.locator' => $fileLocator,
            'templating.name_parser' => $templateNameParser,
        ]);

        return $container;
    }
}
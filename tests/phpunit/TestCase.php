<?php

namespace LAG\AdminBundle\Tests;

use LAG\AdminBundle\Action\ActionInterface;
use LAG\AdminBundle\Action\Configuration\ActionConfiguration;
use LAG\AdminBundle\Admin\Admin;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Admin\Configuration\AdminConfiguration;
use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Controller\AdminAction;
use LAG\AdminBundle\Metadata\Action;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionClass;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use function Symfony\Component\String\u;

class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * Assert that the given service class is configured in the services.yaml.
     */
    protected function assertServiceExists(string $serviceClass): void
    {
        $containerBuilder = $this->buildContainer();
        $exists = false;

        foreach ($containerBuilder->getDefinitions() as $id => $definition) {
            if ($serviceClass === $definition->getClass()) {
                $exists = true;
            }

            if ($serviceClass === $id) {
                $exists = true;
            }
        }
        if ($containerBuilder->hasAlias($serviceClass)) {
            $exists = true;
        }

        $this->assertTrue($exists, 'Failed asserting that the service "'.$serviceClass.'" exists');
    }

    protected function assertServiceHasTag(string $serviceId, string $tag): void
    {
        $containerBuilder = $this->buildContainer();
        $definition = $containerBuilder->getDefinition($serviceId);
        $this->assertTrue($definition->hasTag($tag));
    }

    protected function buildContainer(): ContainerBuilder
    {
        $containerBuilder = new ContainerBuilder();
        $testResourcesDirectory = __DIR__.'/../../config';
        $locator = new FileLocator([
            $testResourcesDirectory,
        ]);
        $loader = new YamlFileLoader($containerBuilder, $locator);
        $finder = new Finder();
        $finder
            ->in($testResourcesDirectory.'/services')
            ->name('*.yaml')
            ->files()
        ;
        $loader->load('services.yaml');

        return $containerBuilder;
    }

    /**
     * Assert that methods declared in the getSubscribedEvents() really exists.
     */
    protected function assertSubscribedMethodsExists(EventSubscriberInterface $subscriber)
    {
        $methods = forward_static_call([
            get_class($subscriber),
            'getSubscribedEvents',
        ]);
        $this->assertIsArray($methods);

        foreach ($methods as $method) {
            if (is_array($method)) {
                $this->assertArrayHasKey(0, $method);
                $method = $method[0];
            } else {
                $this->assertIsString($method);
            }
            $this->assertTrue(method_exists($subscriber, $method));
        }
    }

    protected function setPrivateProperty($object, $property, $value)
    {
        $reflection = new ReflectionClass($object);

        $property = $reflection->getProperty($property);
        $property->setValue($object, $value);
    }

    protected function getPrivateProperty($object, $property)
    {
        $reflection = new ReflectionClass($object);
        $property = $reflection->getProperty($property);

        return $property->getValue($object);
    }

    protected function getAdminDefaultConfiguration(
        string $adminName = 'an_admin_name',
        string $entityClass = 'AnEntityClass',
    ): array {
        return [
            'name' => $adminName,
            'entity' => $entityClass,
            'group' => null,
            'actions' => [
                'create' => [
                    'admin_name' => $adminName,
                ],
                'update' => [
                    'admin_name' => $adminName,
                    'route_parameters' => ['id' => null],
                ],
                'index' => [
                    'admin_name' => $adminName,
                ],
                'delete' => [
                    'admin_name' => $adminName,
                    'route_parameters' => ['id' => null],
                ],
            ],
            'controller' => AdminAction::class,
            'admin_class' => Admin::class,
            'action_class' => Action::class,
            'routes_pattern' => 'lag_admin.{admin}.{action}',
            'pager' => 'pagerfanta',
            'max_per_page' => 25,
            'page_parameter' => 'page',
            'permissions' => 'ROLE_ADMIN',
            'date_format' => 'Y-m-d',
            'data_provider' => 'doctrine',
            'data_persister' => 'doctrine',
            'create_template' => '@LAGAdmin/crud/create.html.twig',
            'update_template' => '@LAGAdmin/crud/update.html.twig',
            'list_template' => '@LAGAdmin/crud/list.html.twig',
            'delete_template' => '@LAGAdmin/crud/delete.html.twig',
            'title' => u($adminName)->camel()->title()->toString(),
            'index_actions' => [
                'create' => [
                    'route' => null,
                    'route_parameters' => [],
                    'admin' => $adminName,
                    'url' => null,
                    'action' => 'create',
                    'text' => 'lag_admin.actions.create',
                    'attr' => [],
                ],
            ],
            'item_actions' => [
                'update' => [
                    'route' => null,
                    'route_parameters' => [],
                    'admin' => $adminName,
                    'url' => null,
                    'action' => 'update',
                    'text' => 'lag_admin.actions.update',
                    'attr' => [],
                ],
                'delete' => [
                    'route' => null,
                    'route_parameters' => [],
                    'admin' => $adminName,
                    'url' => null,
                    'action' => 'delete',
                    'text' => 'lag_admin.actions.delete',
                    'attr' => [],
                ],
            ],
        ];
    }

    protected function createActionConfigurationMock(array $map)
    {
        $actionConfiguration = $this->createMock(ActionConfiguration::class);
        $actionConfiguration
            ->expects($this->atLeastOnce())
            ->method('get')
            ->willReturnMap($map)
        ;

        return $actionConfiguration;
    }

    /**
     * @return MockObject|AdminConfiguration
     */
    protected function createAdminConfigurationMock(array $map): MockObject
    {
        $adminConfiguration = $this->createMock(AdminConfiguration::class);
        $adminConfiguration
            ->expects($this->atLeastOnce())
            ->method('get')
            ->willReturnMap($map)
        ;

        return $adminConfiguration;
    }

    protected function createApplicationConfigurationMock(array $map): MockObject
    {
        $applicationConfiguration = $this->createMock(ApplicationConfiguration::class);
        $applicationConfiguration
            ->method('get')
            ->willReturnMap($map)
        ;

        return $applicationConfiguration;
    }

    /**
     * @param int   $expectedCalls
     * @param int   $configurationExpectedCalls
     *
     * @return MockObject|ActionInterface
     */
    protected function createActionWithConfigurationMock(
        array $map
    ): MockObject {
        $configuration = $this->createActionConfigurationMock($map);

        $action = $this->createMock(ActionInterface::class);
        $action
            ->expects($this->atLeastOnce())
            ->method('getConfiguration')
            ->willReturn($configuration)
        ;

        return $action;
    }

    /**
     * @param int   $expectedCalls
     * @param int   $configurationExpectedCalls
     *
     * @return MockObject|AdminInterface
     */
    protected function createAdminWithConfigurationMock(
        array $map = [],
        Request $request = null
    ): MockObject {
        $configuration = $this->createAdminConfigurationMock($map);

        $admin = $this->createMock(AdminInterface::class);
        $admin
            ->expects($this->atLeastOnce())
            ->method('getConfiguration')
            ->willReturn($configuration)
        ;

        if (null !== $request) {
            $admin
                ->expects($this->atLeastOnce())
                ->method('getRequest')
                ->willReturn($request)
            ;
        }

        return $admin;
    }

    protected function createContainerDefinition(string $class): Definition
    {
        return new Definition($class);
    }

    protected function createApplicationConfiguration(array $applicationConfiguration): ApplicationConfiguration
    {
        return new ApplicationConfiguration($applicationConfiguration);
    }
}

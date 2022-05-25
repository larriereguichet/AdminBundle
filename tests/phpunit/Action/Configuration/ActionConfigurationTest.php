<?php

namespace LAG\AdminBundle\Tests\Action\Configuration;

use JK\Configuration\Exception\InvalidConfigurationException;
use LAG\AdminBundle\Action\Configuration\ActionConfiguration;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Controller\AdminAction;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Metadata\Action;
use LAG\AdminBundle\Tests\TestCase;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ActionConfigurationTest extends TestCase
{
    public function testDefaultMain(): void
    {
        $configuration = new ActionConfiguration();
        $configuration->configure([
            'name' => 'index',
            'admin_name' => 'my_admin',
            'route' => '/my_admin/index',
        ]);

        $this->assertEquals([
            'name' => 'index',
            'admin_name' => 'my_admin',
            'route' => '/my_admin/index',
            'title' => 'Index',
            'icon' => null,
            'action_class' => 'LAG\AdminBundle\Metadata\Action',
            'template' => '@LAGAdmin/crud/list.html.twig',
            'permissions' => [
                'ROLE_ADMIN',
            ],
            'controller' => 'LAG\AdminBundle\Controller\AdminAction',
            'route_parameters' => [],
            'load_strategy' => 'strategy_multiple',
            'path' => 'my-admins',
            'target_route' => 'index',
            'target_route_parameters' => [],
            'fields' => [],
            'order' => [],
            'criteria' => [],
            'filters' => [],
            'repository_method' => null,
            'pager' => 'pagerfanta',
            'max_per_page' => 25,
            'page_parameter' => 'page',
            'date_format' => 'Y-m-d',
            'form' => null,
            'form_options' => [],
        ], $configuration->toArray());
    }

    public function testDefaultConfiguration(): void
    {
        $configuration = new ActionConfiguration();
        $configuration->configure([
            'name' => 'my_action',
            'admin_name' => 'my_admin',
            'fields' => [],
            'path' => '/my-action',
            'route' => 'my_action',
            'template' => 'template.html.twig',
        ]);

        $this->assertEquals([
            'name' => 'my_action',
            'admin_name' => 'my_admin',
            'title' => 'MyAction',
            'icon' => null,
            'action_class' => Action::class,
            'controller' => AdminAction::class,
            'path' => '/my-action',
            'route' => 'my_action',
            'route_parameters' => [],
            'order' => [],
            'criteria' => [],
            'filters' => [],
            'permissions' => ['ROLE_ADMIN'],
            'load_strategy' => 'strategy_unique',
            'repository_method' => null,
            'pager' => 'pagerfanta',
            'max_per_page' => 25,
            'page_parameter' => 'page',
            'date_format' => 'Y-m-d',
            'form' => null,
            'form_options' => [],
            'fields' => [],
            'template' => 'template.html.twig',
            'target_route' => 'index',
            'target_route_parameters' => [],
        ], $configuration->toArray());
    }

    public function testGetters(): void
    {
        $configuration = new ActionConfiguration();
        $configuration->configure([
            'name' => 'my_action',
            'admin_name' => 'my_admin',
            'fields' => [],
            'path' => '/my-action',
            'route' => 'my_action',
            'template' => 'template.html.twig',
        ]);

        $this->assertEquals('my_action', $configuration->getName());
        $this->assertEquals('my_admin', $configuration->getAdminName());
        $this->assertEquals('MyAction', $configuration->getTitle());
        $this->assertEquals('', $configuration->getIcon());
        $this->assertEquals(Action::class, $configuration->getActionClass());

        $this->assertEquals(AdminAction::class, $configuration->getController());
        $this->assertEquals('/my-action', $configuration->getPath());
        $this->assertEquals('my_action', $configuration->getRoute());
        $this->assertEquals('index', $configuration->getTargetRoute());
        $this->assertEquals([], $configuration->getTargetRouteParameters());
        $this->assertEquals([], $configuration->getRouteParameters());

        $this->assertEquals([], $configuration->getOrder());
        $this->assertEquals([], $configuration->getCriteria());
        $this->assertEquals([], $configuration->getFilters());

        $this->assertEquals(['ROLE_ADMIN'], $configuration->getPermissions());

        $this->assertEquals('strategy_unique', $configuration->getLoadStrategy());
        $this->assertEquals('pagerfanta', $configuration->getPager());
        $this->assertEquals(25, $configuration->getMaxPerPage());
        $this->assertEquals('page', $configuration->getPageParameter());
        $this->assertEquals(null, $configuration->getRepositoryMethod());

        $this->assertEquals('Y-m-d', $configuration->getDateFormat());

        $this->assertEquals(null, $configuration->getForm());
        $this->assertEquals([], $configuration->getFormOptions());

        $this->assertEquals([], $configuration->getFields());
        $this->assertEquals('template.html.twig', $configuration->getTemplate());
    }

    public function testWithoutConfiguration(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $configuration = new ActionConfiguration();
        $configuration->configure();
    }

    public function testWithoutPagination(): void
    {
        $configuration = new ActionConfiguration();
        $configuration->configure([
            'name' => 'my_action',
            'admin_name' => 'my_admin',
            'fields' => [],
            'path' => '/my-action',
            'route' => 'my_action',
            'template' => 'template.html.twig',
            'pager' => false,
        ]);

        $this->assertFalse($configuration->isPaginationEnabled());

        $this->expectException(Exception::class);
        $configuration->getPager();
    }

    public function testMaxPerPageWithoutPagination(): void
    {
        $configuration = new ActionConfiguration();
        $configuration->configure([
            'name' => 'my_action',
            'admin_name' => 'my_admin',
            'fields' => [],
            'path' => '/my-action',
            'route' => 'my_action',
            'template' => 'template.html.twig',
            'pager' => false,
        ]);

        $this->assertFalse($configuration->isPaginationEnabled());

        $this->expectException(Exception::class);
        $configuration->getMaxPerPage();
    }

    /**
     * @dataProvider pathPathNormalizerDataProvider
     */
    public function testPathNormalizer(array $actionConfiguration, string $expectedPath): void
    {
        $configuration = new ActionConfiguration();
        $configuration->configure($actionConfiguration);
        $this->assertEquals($expectedPath, $configuration->getPath());
    }

    public function pathPathNormalizerDataProvider(): array
    {
        return [
            [
                [
                    'name' => 'my_action',
                    'admin_name' => 'my_admin',
                    'fields' => [],
                    'path' => null,
                    'route' => 'my_action',
                    'template' => 'template.html.twig',
                ],
                'my-admins/{id}/my-action',
            ],
            [
                [
                    'name' => 'index',
                    'admin_name' => 'category',
                    'fields' => [],
                    'path' => null,
                    'route' => 'my_action',
                    'template' => 'template.html.twig',
                ],
                'categories',
            ],
            [
                [
                    'name' => 'bamboo',
                    'admin_name' => 'category',
                    'fields' => [],
                    'path' => null,
                    'load_strategy' => AdminInterface::LOAD_STRATEGY_NONE,
                    'route' => 'my_action',
                    'template' => 'template.html.twig',
                ],
                'categories/bamboo',
            ],
            [
                [
                    'name' => 'bamboo',
                    'admin_name' => 'category',
                    'fields' => [],
                    'path' => '/end-with-slash/',
                    'load_strategy' => AdminInterface::LOAD_STRATEGY_NONE,
                    'route' => 'my_action',
                    'template' => 'template.html.twig',
                ],
                '/end-with-slash',
            ],
        ];
    }

    public function testFieldNormalizer(): void
    {
        $configuration = new ActionConfiguration();
        $configuration->configure([
            'name' => 'my_action',
            'admin_name' => 'my_admin',
            'fields' => [
                'name' => null,
            ],
            'path' => '/my-action',
            'route' => 'my_action',
            'template' => 'template.html.twig',
        ]);

        $this->assertEquals(['name' => []], $configuration->getFields());
    }

    public function testOrderNormalizer(): void
    {
        $configuration = new ActionConfiguration();
        $this->expectException(InvalidConfigurationException::class);
        $configuration->configure([
            'name' => 'my_action',
            'admin_name' => 'my_admin',
            'fields' => [],
            'order' => [
                'name' => 666,
            ],
            'path' => '/my-action',
            'route' => 'my_action',
            'template' => 'template.html.twig',
        ]);
    }

    /**
     * @dataProvider loadStrategyDataProvider
     */
    public function testLoadStrategyNormalizer(string $actionName, string $expectedLoadStrategy): void
    {
        $configuration = new ActionConfiguration();
        $configuration->configure([
            'name' => $actionName,
            'admin_name' => 'my_admin',
            'fields' => [],
            'path' => '/my-action',
            'route' => 'my_action',
            'template' => 'template.html.twig',
        ]);
        $this->assertEquals($expectedLoadStrategy, $configuration->getLoadStrategy());
    }

    public function loadStrategyDataProvider(): array
    {
        return [
            ['create', AdminInterface::LOAD_STRATEGY_NONE],
            ['update', AdminInterface::LOAD_STRATEGY_UNIQUE],
            ['index', AdminInterface::LOAD_STRATEGY_MULTIPLE],
            ['delete', AdminInterface::LOAD_STRATEGY_UNIQUE],
            ['custom', AdminInterface::LOAD_STRATEGY_UNIQUE],
        ];
    }

    /**
     * @dataProvider filterNormalizerDataProvider
     */
    public function testFiltersNormalizer(array $filters, array $expectedFilters): void
    {
        $configuration = new ActionConfiguration();
        $configuration->configure([
            'name' => 'my_action',
            'admin_name' => 'my_admin',
            'fields' => [],
            'filters' => $filters,
            'path' => '/my-action',
            'route' => 'my_action',
            'template' => 'template.html.twig',
        ]);
        $this->assertEquals($expectedFilters, $configuration->getFilters());
    }

    public function filterNormalizerDataProvider(): array
    {
        return [
            [
                ['id'],
                [
                    'id' => [
                        'name' => 'id',
                        'type' => TextType::class,
                        'options' => [],
                        'comparator' => 'like',
                        'operator' => 'or',
                        'path' => null,
                    ],
                ],
            ],
            [
                ['name' => null],
                [
                    'name' => [
                        'name' => 'name',
                        'type' => TextType::class,
                        'options' => [],
                        'comparator' => 'like',
                        'operator' => 'or',
                        'path' => null,
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider parametersNormalizerDataProvider
     */
    public function testParametersNormalizer(
        string $actionName,
        array $routeParameters,
        array $expectedParameters
    ): void {
        $configuration = new ActionConfiguration();
        $configuration->configure([
            'name' => $actionName,
            'admin_name' => 'my_admin',
            'fields' => [],
            'path' => '/my-action',
            'route' => 'my_action',
            'route_parameters' => $routeParameters,
            'template' => 'template.html.twig',
        ]);

        $this->assertEquals($expectedParameters, $configuration->getRouteParameters());
    }

    public function parametersNormalizerDataProvider(): array
    {
        return [
            ['my_action', ['id' => null], ['id' => null]],
            ['update', [], ['id' => null]],
        ];
    }

    public function testTemplateNormalizerWithWrongTemplate(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $configuration = new ActionConfiguration();
        $configuration->configure([
            'name' => 'custom_action',
            'admin_name' => 'my_admin',
            'fields' => [],
            'path' => '/my-action',
            'route' => 'my_action',
            'route_parameters' => [],
        ]);
    }
}

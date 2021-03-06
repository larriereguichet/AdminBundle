<?php

namespace LAG\AdminBundle\Tests\Configuration;

use JK\Configuration\Exception\InvalidConfigurationException;
use LAG\AdminBundle\Admin\Action;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Configuration\ActionConfiguration;
use LAG\AdminBundle\Controller\AdminAction;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Form\Type\AdminType;
use LAG\AdminBundle\Form\Type\DeleteType;
use LAG\AdminBundle\Tests\TestCase;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ActionConfigurationTest extends TestCase
{
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
            'action_class' => 'LAG\AdminBundle\Admin\Action',
            'controller' => 'LAG\AdminBundle\Controller\AdminAction',
            'path' => '/my-action',
            'route' => 'my_action',
            'route_parameters' => [],
            'order' => [],
            'criteria' => [],
            'filters' => [],
            'permissions' => ['ROLE_ADMIN'],
            'export' => [
                'csv',
                'xml',
                'yaml',
            ],
            'load_strategy' => 'strategy_unique',
            'repository_method' => null,
            'pager' => 'pagerfanta',
            'max_per_page' => 25,
            'page_parameter' => 'page',
            'date_format' => 'Y-m-d',
            'form' => null,
            'form_options' => [],
            'menus' => [],
            'fields' => [],
            'template' => 'template.html.twig',
            'redirect' => null,
            'add_return_link' => true,
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
        $this->assertEquals([], $configuration->getRouteParameters());

        $this->assertEquals([], $configuration->getOrder());
        $this->assertEquals([], $configuration->getCriteria());
        $this->assertEquals([], $configuration->getFilters());

        $this->assertEquals(['ROLE_ADMIN'], $configuration->getPermissions());
        $this->assertEquals([
            'csv',
            'xml',
            'yaml',
        ], $configuration->getExport());

        $this->assertEquals('strategy_unique', $configuration->getLoadStrategy());
        $this->assertEquals('pagerfanta', $configuration->getPager());
        $this->assertEquals(25, $configuration->getMaxPerPage());
        $this->assertEquals('page', $configuration->getPageParameter());

        $this->assertEquals('Y-m-d', $configuration->getDateFormat());

        $this->assertEquals(null, $configuration->getForm());
        $this->assertEquals([], $configuration->getFormOptions());

        $this->assertEquals([], $configuration->getMenus());

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
                    'name' => 'list',
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
            ['edit', AdminInterface::LOAD_STRATEGY_UNIQUE],
            ['list', AdminInterface::LOAD_STRATEGY_MULTIPLE],
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
     * @dataProvider filterFormDataProviderNormalizer
     */
    public function testFilterFormNormalizer($form, string $actionName, ?string $expectedForm): void
    {
        $configuration = new ActionConfiguration();
        $configuration->configure([
            'name' => $actionName,
            'admin_name' => 'my_admin',
            'fields' => [],
            'form' => $form,
            'path' => '/my-action',
            'route' => 'my_action',
            'template' => 'template.html.twig',
        ]);

        $this->assertEquals($expectedForm, $configuration->getForm());
    }

    public function filterFormDataProviderNormalizer(): array
    {
        return [
            [null, 'my_action',  null],
            [false, 'my_action',  null],
            [null, 'create',  AdminType::class],
            ['MyForm', 'create',  'MyForm'],
            [null, 'edit',  AdminType::class],
            [null, 'delete',  DeleteType::class],
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
            ['edit', [], ['id' => null]],
        ];
    }
}

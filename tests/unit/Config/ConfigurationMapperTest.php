<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Config;

use LAG\AdminBundle\Config\ConfigurationMapper;
use LAG\AdminBundle\Metadata\Application;
use LAG\AdminBundle\Metadata\Boolean;
use LAG\AdminBundle\Metadata\Create;
use LAG\AdminBundle\Metadata\Delete;
use LAG\AdminBundle\Metadata\Grid;
use LAG\AdminBundle\Metadata\Index;
use LAG\AdminBundle\Metadata\Resource;
use LAG\AdminBundle\Metadata\Show;
use LAG\AdminBundle\Metadata\Text;
use LAG\AdminBundle\Metadata\Update;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ConfigurationMapperTest extends TestCase
{
    private ConfigurationMapper $configurationMapper;

    #[Test]
    public function itConvertsAnApplicationToAnArray(): void
    {
        $application = new Application(
            name: 'My application',
            dateFormat: 'Y-m-d',
            timeFormat: 'H:i:s',
            translationDomain: 'messages',
            translationPattern: '{application}.{message}',
            routePattern: '{application}.{resource}.{operation}',
            baseTemplate: 'some_template.html.twig',
            permissions: ['ROLE_ADMIN'],
        );

        $data = $this->configurationMapper->fromApplication($application);

        self::assertEquals([
            'name' => 'My application',
            'date_format' => 'Y-m-d',
            'time_format' => 'H:i:s',
            'translation_domain' => 'messages',
            'translation_pattern' => '{application}.{message}',
            'route_pattern' => '{application}.{resource}.{operation}',
            'base_template' => 'some_template.html.twig',
            'permissions' => ['ROLE_ADMIN'],
        ], $data);
    }

    #[Test]
    public function itConvertsAnArrayToAnApplication(): void
    {
        $data = [
            'name' => 'My application',
            'date_format' => 'Y-m-d',
            'time_format' => 'H:i:s',
            'translation_domain' => 'messages',
            'translation_pattern' => '{application}.{message}',
            'route_pattern' => '{application}.{resource}.{operation}',
            'base_template' => 'some_template.html.twig',
            'permissions' => ['ROLE_ADMIN'],
        ];

        $application = $this->configurationMapper->toApplication($data);

        $expectedApplication = new Application(
            name: 'My application',
            dateFormat: 'Y-m-d',
            timeFormat: 'H:i:s',
            translationDomain: 'messages',
            translationPattern: '{application}.{message}',
            routePattern: '{application}.{resource}.{operation}',
            baseTemplate: 'some_template.html.twig',
            permissions: ['ROLE_ADMIN'],
        );

        self::assertEquals($expectedApplication, $application);
    }

    #[Test]
    #[DataProvider('resources')]
    public function itConvertsAResourceToAnArray(Resource $resource): void
    {
        $data = $this->configurationMapper->fromResource($resource);

        self::assertEquals($resource->getName(), $data['name']);
        self::assertEquals($resource->getResourceClass(), $data['resource_class']);
        self::assertEquals($resource->getTitle(), $data['title']);
        self::assertEquals($resource->getGroup(), $data['group']);
        self::assertEquals($resource->getIcon(), $data['icon']);
        self::assertEquals($resource->getProvider(), $data['provider']);
        self::assertEquals($resource->getProcessor(), $data['processor']);
        self::assertEquals($resource->getIdentifiers(), $data['identifiers']);
        self::assertEquals($resource->getRoutePattern(), $data['route_pattern']);
        self::assertEquals($resource->getTranslationPattern(), $data['translation_pattern']);
        self::assertEquals($resource->getTranslationDomain(), $data['translation_domain']);
        self::assertEquals($resource->getPathPrefix(), $data['path_prefix']);
        self::assertEquals($resource->getPermissions(), $data['permissions']);
        self::assertEquals($resource->getForm(), $data['form']);
        self::assertEquals($resource->getFormOptions(), $data['form_options']);
        self::assertEquals($resource->getFormTemplate(), $data['form_template']);
        self::assertEquals($resource->hasValidation(), $data['validation']);
        self::assertEquals($resource->getValidationContext(), $data['validation_context']);
        self::assertEquals($resource->hasAjax(), $data['ajax']);
        self::assertEquals($resource->getNormalizationContext(), $data['normalization_context']);
        self::assertEquals($resource->getDenormalizationContext(), $data['denormalization_context']);
        self::assertEquals($resource->getInput(), $data['input']);
        self::assertEquals($resource->getOutput(), $data['output']);

        foreach ($resource->getOperations() as $key => $operation) {
            self::assertEquals($data['operations'][$key]['class'], $operation::class);
            self::assertEquals($data['operations'][$key]['name'], $operation->getName());
            self::assertEquals($data['operations'][$key]['processor'], $operation->getProcessor());
            self::assertEquals($data['operations'][$key]['provider'], $operation->getProvider());
        }

        foreach ($resource->getProperties() as $property) {
            self::assertEquals($data['properties'][$property->getName()]['class'], $property::class);
            self::assertEquals($data['properties'][$property->getName()]['name'], $property->getName());
        }
    }

    #[Test]
    #[DataProvider('resources')]
    public function itConvertsAnArrayToAResource(Resource $expectedResource): void
    {
        $operations = [
            [
                'class' => Index::class,
                'provider' => 'IndexProvider',
                'processor' => 'IndexProcessor',
                'collection_actions' => null,
            ],
            [
                'class' => Show::class,
                'provider' => 'ShowProvider',
            ],
            [
                'class' => Create::class,
                'provider' => 'CreateProvider',
                'processor' => 'CreateProcessor',
            ],
            [
                'class' => Update::class,
                'provider' => 'UpdateProvider',
                'processor' => 'UpdateProcessor',
            ],
            [
                'class' => Delete::class,
                'provider' => 'DeleteProvider',
                'processor' => 'DeleteProcessor',
            ],
        ];

        $properties = [
            'label' => [
                'class' => Text::class,
                'name' => 'label',
                'translatable' => true,
                'translation_domain' => 'messages',
            ],
            [
                'name' => 'enabled',
                'class' => Boolean::class,
            ],
        ];

        $data = [
            'class' => $expectedResource::class,
            'name' => 'MyResource',
            'application' => 'admin',
            'resource_class' => 'MyEntity',
            'title' => 'Some Title',
            'group' => 'some group',
            'icon' => 'icon.png',
            'path_prefix' => '/operations',
            'permissions' => ['ROLE_ADMIN'],
            'operations' => $operations,
            'properties' => $properties,
            'processor' => 'ResourceProcessor',
            'provider' => 'ResourceProvider',
            'identifiers' => ['id'],
            'route_pattern' => '{application}.{resource}.{operation}',
            'translation_pattern' => '{resource}.{operation}',
            'translation_domain' => 'messages',
            'form' => 'MyForm',
            'form_options' => ['an_option' => 'a_value'],
            'form_template' => 'form.html.twig',
            'validation' => true,
            'validation_context' => ['groups' => 'my_group'],
            'ajax' => false,
            'normalization_context' => ['groups' => 'my_group'],
            'denormalization_context' => ['groups' => 'my_group'],
            'input' => 'MyInput',
            'output' => 'MyOutput',
        ];
        $resource = $this->configurationMapper->toResource($data);

        self::assertEquals($expectedResource, $resource);
    }

    public static function resources(): iterable
    {
        $operations = [
            new Index(
                provider: 'IndexProvider',
                processor: 'IndexProcessor',
            ),
            new Show(
                provider: 'ShowProvider',
            ),
            new Create(
                provider: 'CreateProvider',
                processor: 'CreateProcessor',
            ),
            new Update(
                provider: 'UpdateProvider',
                processor: 'UpdateProcessor',
            ),
            new Delete(
                provider: 'DeleteProvider',
                processor: 'DeleteProcessor',
            ),
        ];

        $properties = [
            new Text(
                name: 'label',
                translatable: true,
                translationDomain: 'messages',
            ),
            new Boolean(name: 'enabled'),
        ];

        $resource = new Resource(
            name: 'MyResource',
            resourceClass: 'MyEntity',
            title: 'Some Title',
            group: 'some group',
            icon: 'icon.png',
            operations: $operations,
            properties: $properties,
            provider: 'ResourceProvider',
            processor: 'ResourceProcessor',
            identifiers: ['id'],
            routePattern: '{application}.{resource}.{operation}',
            translationPattern: '{resource}.{operation}',
            translationDomain: 'messages',
            pathPrefix: '/operations',
            permissions: ['ROLE_ADMIN'],
            form: 'MyForm',
            formOptions: ['an_option' => 'a_value'],
            formTemplate: 'form.html.twig',
            validation: true,
            validationContext: ['groups' => 'my_group'],
            ajax: false,
            normalizationContext: ['groups' => 'my_group'],
            denormalizationContext: ['groups' => 'my_group'],
            input: 'MyInput',
            output: 'MyOutput',
        );

        yield [$resource];
    }

    #[Test]
    #[DataProvider('grids')]
    public function itConvertsAGridToAnArray(Grid $grid): void
    {
        $data = $this->configurationMapper->fromGrid($grid);

        self::assertEquals($grid->getName(), $data['name']);
        self::assertEquals($grid->getTitle(), $data['title']);
        self::assertEquals($grid->getType(), $data['type']);
        self::assertEquals($grid->getTemplate(), $data['template']);
        self::assertEquals($grid->getTranslationDomain(), $data['translation_domain']);
        self::assertEquals($grid->getProperties(), $data['properties']);
        self::assertEquals($grid->getAttributes(), $data['attributes']);
        self::assertEquals($grid->getRowAttributes(), $data['row_attributes']);
        self::assertEquals($grid->getContainerAttributes(), $data['container_attributes']);
        self::assertEquals($grid->getActionCellAttributes(), $data['action_cell_attributes']);
        self::assertEquals($grid->getHeaderRowAttributes(), $data['header_row_attributes']);
        self::assertEquals($grid->getHeaderAttributes(), $data['header_attributes']);
        self::assertEquals($grid->getOptions(), $data['options']);
        self::assertEquals($grid->getForm(), $data['form']);
        self::assertEquals($grid->getFormOptions(), $data['form_options']);
        self::assertEquals($grid->getActions(), $data['actions']);
        self::assertEquals($grid->getCollectionActions(), $data['collection_actions']);
        self::assertEquals($grid->getEmptyMessage(), $data['empty_message']);
        self::assertEquals($grid->isSortable(), $data['sortable']);
    }

    public static function grids(): iterable
    {
        $grid = new Grid();

        yield 'empty_grid' => [$grid];

        $grid = new Grid(
            name: 'My little grid',
            title: 'My little title',
            type: 'some_grid_type',
            template: 'some_grid_template.html.twig',
            translationDomain: 'messages',
            properties: [],
            attributes: ['class' => 'my-grid'],
            rowAttributes: ['class' => 'my-row'],
            containerAttributes: ['class' => 'my-container'],
            options: ['an_option' => 'a_value'],
        );

        yield 'basic_grid' => [$grid];
    }

    protected function setUp(): void
    {
        $this->configurationMapper = new ConfigurationMapper();
    }
}

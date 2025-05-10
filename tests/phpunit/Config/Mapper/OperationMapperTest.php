<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Config\Mapper;

use LAG\AdminBundle\Config\Mapper\OperationMapper;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\Create;
use LAG\AdminBundle\Metadata\Delete;
use LAG\AdminBundle\Metadata\Index;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Metadata\Show;
use LAG\AdminBundle\Metadata\TextFilter;
use LAG\AdminBundle\Metadata\Update;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class OperationMapperTest extends TestCase
{
    #[Test]
    #[DataProvider(methodName: 'operationData')]
    public function itCreatesAnOperationFromAnArray(array $data): void
    {
        $mapper = new OperationMapper();
        $operation = $mapper->fromArray($data);

        self::assertInstanceOf($data['class'], $operation);
        self::assertEquals($data['short_name'], $operation->getName());
        self::assertEquals($data['context'], $operation->getContext());
        self::assertEquals($data['title'], $operation->getTitle());
        self::assertEquals($data['description'], $operation->getDescription());
        self::assertEquals($data['icon'], $operation->getIcon());
        self::assertEquals($data['template'], $operation->getTemplate());
        self::assertEquals($data['base_template'], $operation->getBaseTemplate());
        self::assertEquals($data['permissions'], $operation->getPermissions());
        self::assertEquals($data['controller'], $operation->getController());
        self::assertEquals($data['route'], $operation->getRoute());
        self::assertEquals($data['route_parameters'], $operation->getRouteParameters());
        self::assertEquals($data['path'], $operation->getPath());
        self::assertEquals($data['redirect_route'], $operation->getRedirectRoute());
        self::assertEquals($data['redirect_route_parameters'], $operation->getRedirectRouteParameters());
        self::assertEquals($data['processor'], $operation->getProcessor());
        self::assertEquals($data['provider'], $operation->getProvider());
        self::assertEquals($data['methods'], $operation->getMethods());
        self::assertEquals($data['identifiers'], $operation->getIdentifiers());
        self::assertEquals($data['contextual_actions'], $operation->getContextualActions());
        self::assertEquals($data['item_actions'], $operation->getItemActions());
        self::assertEquals($data['redirect_operation'], $operation->getRedirectOperation());
        self::assertEquals($data['validation_context'], $operation->getValidationContext());
        self::assertEquals($data['normalization_context'], $operation->getNormalizationContext());
        self::assertEquals($data['denormalization_context'], $operation->getDenormalizationContext());
        self::assertEquals($data['input'], $operation->getInput());
        self::assertEquals($data['output'], $operation->getOutput());
        self::assertTrue($operation->hasValidation());
        self::assertTrue($operation->hasAjax());
        self::assertFalse($operation->isPartial());

        if (!$operation instanceof Show) {
            self::assertEquals($data['form'], $operation->getForm());
            self::assertEquals($data['form_options'], $operation->getFormOptions());
            self::assertEquals($data['form_template'], $operation->getFormTemplate());
            self::assertEquals($data['workflow'], $operation->getWorkflow());
            self::assertEquals($data['workflow_transition'], $operation->getWorkflowTransition());
            self::assertEquals($data['success_message'], $operation->getSuccessMessage());
        }

        if ($operation instanceof CollectionOperationInterface) {
            self::assertEquals($data['items_per_page'], $operation->getItemsPerPage());
            self::assertEquals($data['criteria'], $operation->getCriteria());
            self::assertEquals($data['order_by'], $operation->getOrderBy());
            self::assertEquals($data['page_parameter'], $operation->getPageParameter());
            self::assertEquals($data['filters'], $operation->getFilters());
            self::assertEquals($data['grid'], $operation->getGrid());
            self::assertEquals($data['grid_options'], $operation->getGridOptions());
            self::assertEquals($data['collection_actions'], $operation->getCollectionActions());
            self::assertEquals($data['item_actions'], $operation->getItemActions());
            self::assertEquals($data['filter_form'], $operation->getFilterForm());
            self::assertEquals($data['filter_form_options'], $operation->getFilterFormOptions());
            self::assertEquals($data['item_form'], $operation->getItemForm());
            self::assertEquals($data['item_form_options'], $operation->getItemFormOptions());
            self::assertEquals($data['collection_form'], $operation->getCollectionForm());
            self::assertEquals($data['collection_form_options'], $operation->getCollectionFormOptions());
        }
    }

    #[Test]
    #[DataProvider(methodName: 'operations')]
    public function itConvertsAnOperationToAnArray(OperationInterface $operation): void
    {
        $mapper = new OperationMapper();
        $data = $mapper->toArray($operation);

        self::assertEquals($operation::class, $data['class']);
        self::assertEquals($operation->getName(), $data['name']);
        self::assertEquals($operation->getContext(), $data['context']);
        self::assertEquals($operation->getTitle(), $data['title']);
        self::assertEquals($operation->getDescription(), $data['description']);
        self::assertEquals($operation->getIcon(), $data['icon']);
        self::assertEquals($operation->getTemplate(), $data['template']);
        self::assertEquals($operation->getBaseTemplate(), $data['base_template']);
        self::assertEquals($operation->getPermissions(), $data['permissions']);
        self::assertEquals($operation->getController(), $data['controller']);
        self::assertEquals($operation->getRoute(), $data['route']);
        self::assertEquals($operation->getRouteParameters(), $data['route_parameters']);
        self::assertEquals($operation->getPath(), $data['path']);
        self::assertEquals($operation->getRedirectRoute(), $data['redirect_route']);
        self::assertEquals($operation->getRedirectRouteParameters(), $data['redirect_route_parameters']);
        self::assertEquals($operation->getForm(), $data['form']);
        self::assertEquals($operation->getFormOptions(), $data['form_options']);
        self::assertEquals($operation->getFormTemplate(), $data['form_template']);
        self::assertEquals($operation->getProcessor(), $data['processor']);
        self::assertEquals($operation->getProvider(), $data['provider']);
        self::assertEquals($operation->getMethods(), $data['methods']);
        self::assertEquals($operation->getIdentifiers(), $data['identifiers']);
        self::assertEquals($operation->getContextualActions(), $data['contextual_actions']);
        self::assertEquals($operation->getItemActions(), $data['item_actions']);
        self::assertEquals($operation->getRedirectOperation(), $data['redirect_operation']);
        self::assertEquals($operation->getValidationContext(), $data['validation_context']);
        self::assertEquals($operation->getNormalizationContext(), $data['normalization_context']);
        self::assertEquals($operation->getDenormalizationContext(), $data['denormalization_context']);
        self::assertEquals($operation->getInput(), $data['input']);
        self::assertEquals($operation->getOutput(), $data['output']);
        self::assertEquals($operation->getWorkflow(), $data['workflow']);
        self::assertEquals($operation->getWorkflowTransition(), $data['workflow_transition']);
        self::assertEquals($operation->getSuccessMessage(), $data['success_message']);

        if ($operation instanceof CollectionOperationInterface) {
            self::assertCount(\count($operation->getFilters()), $data['filters']);

            foreach ($data['filters'] as $index => $filter) {
                self::assertEquals($data['filters'][$index]['name'], $operation->getFilters()[$index]->getName());
            }
        }
    }

    public static function operationData(): iterable
    {
        $data = [
            'name' => 'My operation',
            'context' => ['key' => 'value'],
            'title' => 'A title',
            'description' => 'A description',
            'icon' => 'icon.png',
            'template' => 'template.html.twig',
            'base_template' => 'base.html.twig',
            'permissions' => ['ROLE_USER'],
            'controller' => 'MyController',
            'route' => 'some_route',
            'route_parameters' => ['param' => 'value'],
            'path' => '/operation',
            'redirect_route' => '/redirect',
            'redirect_route_parameters' => ['param' => 'value'],
            'processor' => 'MyProcessor',
            'provider' => 'MyProvider',
            'methods' => ['GET', 'POST'],
            'identifiers' => ['id', 'name'],
            'contextual_actions' => [],
            'item_actions' => [],
            'redirect_operation' => 'my_application.my_resource.my_operation',
            'validation' => true,
            'validation_context' => ['group' => 'my_group'],
            'use_ajax' => true,
            'normalization_context' => ['group' => 'my_group'],
            'denormalization_context' => ['group' => 'my_group'],
            'input' => 'MyInput',
            'output' => 'MyOutput',
            'partial' => false,
            'success_message' => 'OK',
        ];
        $data['class'] = Show::class;
        yield 'show' => [$data];

        $data += [
            'form' => 'MyForm',
            'form_options' => ['an_option' => 'value'],
            'form_template' => 'form.html.twig',
            'workflow' => 'some_workflow',
            'workflow_transition' => 'some_workflow_transition',
        ];

        $data['class'] = Create::class;
        yield 'create' => [$data];

        $data['class'] = Update::class;
        yield 'update' => [$data];

        $data['class'] = Delete::class;
        yield 'delete' => [$data];

        $data += [
            'items_per_page' => 69,
            'page_parameter' => '__page',
            'criteria' => ['id' => 32],
            'order_by' => ['id' => 'desc'],
            'filters' => [],
            'grid' => 'my_grid',
            'grid_options' => ['an_option' => 'value'],
            'collection_actions' => [],
            'item_form' => 'MyForm',
            'item_form_options' => ['an_option' => 'value'],
            'collection_form' => 'MyForm',
            'collection_form_options' => ['an_option' => 'value'],
            'filter_form' => 'MyFilterForm',
            'filter_form_options' => ['an_option' => 'value'],
        ];

        $data['class'] = Index::class;
        yield 'index' => [$data];
    }

    public static function operations(): iterable
    {
        $operation = new Index(filters: [
            new TextFilter(name: 'search', properties: ['name', 'title']),
        ]);
        yield 'index' => [$operation];

        yield 'show' => [new Show(
            shortName: 'My operation',
            context: ['key' => 'value'],
            title: 'My title',
            description: 'A description',
            icon: 'icon.png',
            template: 'template.html.twig',
            baseTemplate: 'base.html.twig',
            permissions: ['ROLE_USER'],
            controller: 'MyController',
            route: 'my_route',
            routeParameters: ['param' => 'value'],
            methods: ['GET', 'POST'],
            path: '/operation',
            redirectRoute: 'redirect_route',
            redirectRouteParameters: ['param' => 'value'],
            processor: 'MyProcessor',
            provider: 'MyProvider',
            identifiers: ['id'],
            contextualActions: [],
            itemActions: [],
            redirectOperation: 'my_application.my_resource.my_operation',
            validation: true,
            validationContext: ['group' => 'my_group'],
            ajax: true,
            normalizationContext: ['group' => 'my_group'],
            denormalizationContext: ['group' => 'my_group'],
            input: 'MyInput',
            output: 'MyOutput',
            partial: true,
            successMessage: 'OK',
        )];

        yield 'create' => [new Create(
            shortName: 'My operation',
            context: ['key' => 'value'],
            title: 'My title',
            description: 'A description',
            icon: 'icon.png',
            template: 'template.html.twig',
            baseTemplate: 'base.html.twig',
            permissions: ['ROLE_USER'],
            controller: 'MyController',
            route: 'my_route',
            routeParameters: ['param' => 'value'],
            methods: ['GET', 'POST'],
            path: '/operation',
            redirectRoute: 'redirect_route',
            redirectRouteParameters: ['param' => 'value'],
            processor: 'MyProcessor',
            provider: 'MyProvider',
            identifiers: ['id'],
            contextualActions: [],
            itemActions: [],
            redirectOperation: 'my_application.my_resource.my_operation',
            validation: true,
            validationContext: ['group' => 'my_group'],
            ajax: true,
            normalizationContext: ['group' => 'my_group'],
            denormalizationContext: ['group' => 'my_group'],
            input: 'MyInput',
            output: 'MyOutput',
            partial: true,
            successMessage: 'OK',
        )];

        yield 'update' => [new Update(
            shortName: 'My operation',
            context: ['key' => 'value'],
            title: 'My title',
            description: 'A description',
            icon: 'icon.png',
            template: 'template.html.twig',
            baseTemplate: 'base.html.twig',
            permissions: ['ROLE_USER'],
            controller: 'MyController',
            route: 'my_route',
            routeParameters: ['param' => 'value'],
            methods: ['GET', 'POST'],
            path: '/operation',
            redirectRoute: 'redirect_route',
            redirectRouteParameters: ['param' => 'value'],
            form: 'MyForm',
            formOptions: ['an_option' => 'value'],
            formTemplate: 'form.html.twig',
            processor: 'MyProcessor',
            provider: 'MyProvider',
            identifiers: ['id'],
            contextualActions: [],
            itemActions: [],
            redirectOperation: 'my_application.my_resource.my_operation',
            validation: true,
            validationContext: ['group' => 'my_group'],
            ajax: true,
            normalizationContext: ['group' => 'my_group'],
            denormalizationContext: ['group' => 'my_group'],
            input: 'MyInput',
            output: 'MyOutput',
            partial: true,
            successMessage: 'OK',
        )];

        yield 'delete' => [new Delete(
            shortName: 'My operation',
            context: ['key' => 'value'],
            title: 'My title',
            description: 'A description',
            icon: 'icon.png',
            template: 'template.html.twig',
            baseTemplate: 'base.html.twig',
            permissions: ['ROLE_USER'],
            controller: 'MyController',
            route: 'my_route',
            routeParameters: ['param' => 'value'],
            methods: ['GET', 'POST'],
            path: '/operation',
            redirectRoute: 'redirect_route',
            redirectRouteParameters: ['param' => 'value'],
            processor: 'MyProcessor',
            provider: 'MyProvider',
            identifiers: ['id'],
            contextualActions: [],
            itemActions: [],
            redirectOperation: 'my_application.my_resource.my_operation',
            validation: true,
            validationContext: ['group' => 'my_group'],
            ajax: true,
            normalizationContext: ['group' => 'my_group'],
            denormalizationContext: ['group' => 'my_group'],
            input: 'MyInput',
            output: 'MyOutput',
            partial: true,
            successMessage: 'OK',
        )];
    }
}

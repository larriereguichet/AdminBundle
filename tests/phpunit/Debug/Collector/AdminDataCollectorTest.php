<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Debug\Collector;

use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Bridge\Doctrine\ORM\State\ORMDataProcessor;
use LAG\AdminBundle\Bridge\Doctrine\ORM\State\ORMDataProvider;
use LAG\AdminBundle\Controller\Create;
use LAG\AdminBundle\Controller\Delete;
use LAG\AdminBundle\Controller\Index;
use LAG\AdminBundle\Debug\DataCollector\AdminDataCollector;
use LAG\AdminBundle\Form\Type\Resource\DeleteType;
use LAG\AdminBundle\Metadata\AdminResource;
use LAG\AdminBundle\Request\Extractor\ParametersExtractorInterface;
use LAG\AdminBundle\Resource\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminDataCollectorTest extends TestCase
{
    private AdminDataCollector $collector;
    private MockObject $registry;
    private MockObject $applicationConfiguration;
    private MockObject $parametersExtractor;

    public function testCollect(): void
    {
        $request = new Request();
        $response = new Response();

        $resource = (new AdminResource())
            ->withName('my_resource')
        ;

        $this
            ->registry
            ->expects($this->once())
            ->method('all')
            ->willReturn([$resource])
        ;

        $this
            ->applicationConfiguration
            ->expects($this->once())
            ->method('isFrozen')
            ->willReturn(true)
        ;
        $this
            ->applicationConfiguration
            ->expects($this->once())
            ->method('toArray')
            ->willReturn([
                'resource_paths' => '/path',
                'title' => 'A Title',
                'date_format' => 'd/m/Y',
                'time_format' => null,
                'date_localization' => false,
            ])
        ;

        $this
            ->parametersExtractor
            ->expects($this->once())
            ->method('supports')
            ->willReturn(true)
        ;
        $this
            ->parametersExtractor
            ->expects($this->once())
            ->method('getResourceName')
            ->with($request)
            ->willReturn('my_resource')
        ;
        $this
            ->parametersExtractor
            ->expects($this->once())
            ->method('getOperationName')
            ->with($request)
            ->willReturn('my_operation')
        ;

        $this->collector->collect($request, $response);

        $this->assertEquals([
            'resources' => [
                'my_resource' => [
                    'name' => 'my_resource',
                    'dataClass' => null,
                    'title' => null,
                    'group' => null,
                    'icon' => null,
                    'operations' => [
                        'index' => [
                            'name' => 'index',
                            'title' => null,
                            'description' => null,
                            'icon' => null,
                            'template' => '@LAGAdmin/crud/index.html.twig',
                            'permissions' => [],
                            'controller' => Index::class,
                            'route' => null,
                            'routeParameters' => [],
                            'methods' => [],
                            'path' => null,
                            'targetRoute' => null,
                            'targetRouteParameters' => [],
                            'formType' => null,
                            'processor' => ORMDataProcessor::class,
                            'provider' => ORMDataProvider::class,
                            'identifiers' => [],
                        ],
                        'create' => [
                            'name' => 'create',
                            'title' => null,
                            'description' => null,
                            'icon' => null,
                            'template' => '@LAGAdmin/crud/create.html.twig',
                            'permissions' => [],
                            'controller' => Create::class,
                            'route' => null,
                            'routeParameters' => null,
                            'methods' => ['POST', 'GET'],
                            'path' => null,
                            'targetRoute' => null,
                            'targetRouteParameters' => null,
                            'formType' => null,
                            'processor' => ORMDataProcessor::class,
                            'provider' => ORMDataProvider::class,
                            'identifiers' => [],
                        ],
                        'update' => [
                            'name' => 'update',
                            'title' => null,
                            'description' => null,
                            'icon' => null,
                            'template' => '@LAGAdmin/crud/update.html.twig',
                            'permissions' => [],
                            'controller' => \LAG\AdminBundle\Controller\Update::class,
                            'route' => null,
                            'routeParameters' => null,
                            'methods' => ['POST', 'GET'],
                            'path' => null,
                            'targetRoute' => null,
                            'targetRouteParameters' => null,
                            'formType' => null,
                            'processor' => ORMDataProcessor::class,
                            'provider' => ORMDataProvider::class,
                            'identifiers' => ['id'],
                        ],
                        'delete' => [
                            'name' => 'delete',
                            'title' => null,
                            'description' => null,
                            'icon' => null,
                            'template' => '@LAGAdmin/crud/delete.html.twig',
                            'permissions' => [],
                            'controller' => Delete::class,
                            'route' => null,
                            'routeParameters' => null,
                            'methods' => ['POST', 'GET'],
                            'path' => null,
                            'targetRoute' => null,
                            'targetRouteParameters' => null,
                            'formType' => DeleteType::class,
                            'processor' => ORMDataProcessor::class,
                            'provider' => ORMDataProvider::class,
                            'identifiers' => ['id'],
                        ],
                        'show' => [
                            'name' => 'show',
                            'title' => null,
                            'description' => null,
                            'icon' => null,
                            'template' => '@LAGAdmin/crud/show.html.twig',
                            'permissions' => [],
                            'controller' => \LAG\AdminBundle\Controller\Show::class,
                            'route' => null,
                            'routeParameters' => null,
                            'methods' => ['GET'],
                            'path' => null,
                            'targetRoute' => null,
                            'targetRouteParameters' => null,
                            'formType' => null,
                            'processor' => ORMDataProcessor::class,
                            'provider' => ORMDataProvider::class,
                            'identifiers' => ['id'],
                        ],
                    ],
                    'processor' => ORMDataProcessor::class,
                    'provider' => ORMDataProvider::class,
                    'identifiers' => ['id'],
                    'routePattern' => 'lag_admin.{resource}.{operation}',
                    'routePrefix' => '/{resourceName}',
                    'translationPattern' => null,
                    'translationDomain' => null,
                ],
            ],
            'application' => [
                'resource' => 'my_resource',
                'operation' => 'my_operation',
                'resource_paths' => '/path',
                'title' => 'A Title',
                'date_format' => 'd/m/Y',
                'time_format' => null,
                'date_localization' => false,
            ],
        ], $this->collector->getData());

        $this->collector->reset();
        $this->assertEquals([], $this->collector->getData());
    }

    public function testGetName(): void
    {
        $this->assertEquals(AdminDataCollector::class, $this->collector->getName());
    }

    protected function setUp(): void
    {
        $this->registry = $this->createMock(ResourceRegistryInterface::class);
        $this->applicationConfiguration = $this->createMock(ApplicationConfiguration::class);
        $this->parametersExtractor = $this->createMock(ParametersExtractorInterface::class);
        $this->collector = new AdminDataCollector(
            $this->registry,
            $this->applicationConfiguration,
            $this->parametersExtractor,
        );
    }
}

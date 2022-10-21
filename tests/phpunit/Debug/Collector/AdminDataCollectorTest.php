<?php

namespace LAG\AdminBundle\Tests\Debug\Collector;

use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Bridge\Doctrine\ORM\State\ORMDataProcessor;
use LAG\AdminBundle\Bridge\Doctrine\ORM\State\ORMDataProvider;
use LAG\AdminBundle\Debug\DataCollector\AdminDataCollector;
use LAG\AdminBundle\Metadata\AdminResource;
use LAG\AdminBundle\Metadata\Create;
use LAG\AdminBundle\Metadata\Delete;
use LAG\AdminBundle\Metadata\Index;
use LAG\AdminBundle\Metadata\Show;
use LAG\AdminBundle\Metadata\Update;
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
                        new Index(),
                        new Create(),
                        new Update(),
                        new Delete(),
                        new Show(),
                    ],
                    'processor' => ORMDataProcessor::class,
                    'provider' => ORMDataProvider::class,
                    'identifiers' => ['id'],
                    'routePattern' => 'lag_admin.{resource}.{operation}',
                    'prefix' => '/{resourceName}',
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

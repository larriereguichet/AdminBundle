<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\EventListener\Resource;

use LAG\AdminBundle\Event\Events\ResourceEvent;
use LAG\AdminBundle\EventListener\Resource\InitializeResourceListener;
use LAG\AdminBundle\Metadata\AdminResource;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Routing\Route\RouteNameGeneratorInterface;
use LAG\AdminBundle\Tests\TestCase;
use Symfony\Component\PropertyAccess\PropertyAccess;

class ResourceCreateListenerTest extends TestCase
{
    public static function configurationProvider(): iterable
    {
        $resource = new AdminResource('my_resource');

        // Default application configuration
        yield [$resource, [], [
            'name' => 'my_resource',
            'title' => 'My resources',
            'applicationName' => 'admin',
            'translationDomain' => 'admin',
            'operations[get_collection].resource.name' => 'my_resource',
            'operations[get].resource.name' => 'my_resource',
            'operations[create].resource.name' => 'my_resource',
            'operations[update].resource.name' => 'my_resource',
            'operations[delete].resource.name' => 'my_resource',
            'operations[get_collection].title' => 'My resources',
            'operations[get].title' => 'Get my resource',
            'operations[create].title' => 'Create my resource',
            'operations[update].title' => 'Update my resource',
            'operations[delete].title' => 'Delete my resource',
            'operations[get_collection].route' => 'my_resource_get_collection',
            'operations[get].route' => 'my_resource_get',
            'operations[create].route' => 'my_resource_create',
            'operations[update].route' => 'my_resource_update',
            'operations[delete].route' => 'my_resource_delete',
            'operations[get_collection].routeParameters' => [],
            'operations[get].routeParameters' => ['id' => null],
            'operations[create].routeParameters' => [],
            'operations[update].routeParameters' => ['id' => null],
            'operations[delete].routeParameters' => ['id' => null],
        ]];
    }

    /** @dataProvider configurationProvider */
    public function testDefaultConfiguration(AdminResource $resource, array $configuration, array $expectedValues): void
    {
        $routeNameGenerator = $this->createMock(RouteNameGeneratorInterface::class);
        $routeNameGenerator
            ->expects($this->atLeastOnce())
            ->method('generateRouteName')
            ->willReturnCallback(function (AdminResource $resource, OperationInterface $operation) {
                return $resource->getName().'_'.$operation->getName();
            })
        ;
        $event = new ResourceEvent($resource);

        $listener = new InitializeResourceListener('admin', 'admin', $routeNameGenerator);
        $listener->__invoke($event);
        $accessor = PropertyAccess::createPropertyAccessor();

        foreach ($expectedValues as $getter => $value) {
            $this->assertEquals($value, $accessor->getValue($event->getResource(), $getter));
        }
    }

    public function testService(): void
    {
        $this->assertServiceExists(InitializeResourceListener::class);
    }
}

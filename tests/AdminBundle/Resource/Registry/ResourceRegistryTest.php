<?php

namespace LAG\AdminBundle\Tests\Resource\Registry;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Resource\Registry\ResourceRegistry;
use LAG\AdminBundle\Tests\AdminTestBase;

class ResourceRegistryTest extends AdminTestBase
{
    public function testLoad(): void
    {
        $registry = $this->createRegistry($this->getFixturesPath());

        $this->assertTrue($registry->has('panda'));
        $this->assertCount(1, $registry->all());
        $this->assertCount(1, $registry->keys());

        $resource = $registry->get('panda');

        $this->assertEquals('App\\Entity\\Panda', $resource->getEntityClass());
        $this->assertEquals('panda', $resource->getName());
    }

    public function testGetWrongResource(): void
    {
        $registry = $this->createRegistry($this->getFixturesPath());

        $this->expectException(Exception::class);
        $this->assertFalse($registry->has('wrong'));
        $registry->get('wrong');
    }

    public function testRemove(): void
    {
        $registry = $this->createRegistry($this->getFixturesPath());

        $this->assertCount(1, $registry->all());
        $registry->remove('panda');
        $this->assertCount(0, $registry->all());
    }

    public function testRemoveWrongResource(): void
    {
        $registry = $this->createRegistry($this->getFixturesPath());

        $this->expectException(Exception::class);
        $this->assertFalse($registry->has('wrong'));
        $registry->remove('wrong');
    }

    private function createRegistry(string $path): ResourceRegistry
    {
        return new ResourceRegistry($path);
    }

    private function getFixturesPath(): string
    {
        return realpath(__DIR__.'/../../Fixtures/admin/resources');
    }
}

<?php

namespace LAG\AdminBundle\Tests\Resource;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Resource\AdminResource;
use LAG\AdminBundle\Tests\AdminTestBase;

class AdminResourceTest extends AdminTestBase
{
    public function testResource(): void
    {
        $resource = new AdminResource('panda', [
            'entity' => 'App\\Panda',
        ]);

        $this->assertEquals('panda', $resource->getName());
        $this->assertEquals([
            'entity' => 'App\\Panda',
        ], $resource->getConfiguration());
        $this->assertEquals('App\\Panda', $resource->getEntityClass());
    }

    public function testResourceWithoutEntity(): void
    {
        $this->expectException(Exception::class);
        new AdminResource('wrong', []);
    }
}

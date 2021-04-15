<?php

namespace LAG\AdminBundle\Tests\Admin\Resource\Loader;

use Exception;
use LAG\AdminBundle\Admin\Resource\Loader\ResourceLoader;
use LAG\AdminBundle\Tests\TestCase;

class ResourceLoaderTest extends TestCase
{
    private ResourceLoader $resourceLoader;

    public function testLoad(): void
    {
        $resources = $this->resourceLoader->load($this->getFixturesPath());

        $this->assertCount(1, $resources);
        $this->assertArrayHasKey('panda', $resources);
    }

    public function testLoadWithoutExistingDirectory(): void
    {
        $this->expectException(Exception::class);
        $this->resourceLoader->load('wrong_directory');
    }

    protected function setUp(): void
    {
        $this->resourceLoader = new ResourceLoader();
    }

    private function getFixturesPath(): string
    {
        return realpath(__DIR__.'/../../../Fixtures/admin/resources');
    }
}

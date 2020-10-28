<?php

namespace LAG\AdminBundle\Tests\Admin\Resource\Loader;

use LAG\AdminBundle\Admin\Resource\Loader\ResourceLoader;
use LAG\AdminBundle\Tests\TestCase;

class ResourceLoaderTest extends TestCase
{
    public function testLoad(): void
    {
        $loader = $this->createLoader();
        $resources = $loader->load($this->getFixturesPath());

        $this->assertCount(1, $resources);
        $this->assertArrayHasKey('panda', $resources);
    }

    private function createLoader(): ResourceLoader
    {
        return new ResourceLoader();
    }

    private function getFixturesPath(): string
    {
        return realpath(__DIR__.'/../../../Fixtures/admin/resources');
    }
}

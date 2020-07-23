<?php

namespace LAG\AdminBundle\Tests\Resource\Loader;

use LAG\AdminBundle\Resource\Loader\ResourceLoader;
use LAG\AdminBundle\Tests\AdminTestBase;

class ResourceLoaderTest extends AdminTestBase
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
        return realpath(__DIR__.'/../../Fixtures/admin/resources');
    }
}

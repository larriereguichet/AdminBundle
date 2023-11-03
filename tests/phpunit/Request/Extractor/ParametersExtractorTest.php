<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Request\Extractor;

use LAG\AdminBundle\Request\Extractor\ParametersExtractor;
use LAG\AdminBundle\Tests\TestCase;
use Symfony\Component\HttpFoundation\Request;

class ParametersExtractorTest extends TestCase
{
    private ParametersExtractor $extractor;

    public function testGetAdminName(): void
    {
        $adminName = $this->extractor->getResourceName(new Request([], [], [
            '_resource' => 'my_admin',
            '_operation' => 'my_action',
        ]));

        $this->assertEquals('my_admin', $adminName);
    }

    public function testGetAdminNameWithException(): void
    {
        $resource = $this->extractor->getResourceName(new Request([], [], [
            '_route_params' => [],
        ]));
        $this->assertNull($resource);
    }

    public function testGetActionName(): void
    {
        $actionName = $this->extractor->getOperationName(new Request([], [], [
            '_resource' => 'my_admin',
            '_operation' => 'my_action',
        ]));

        $this->assertEquals('my_action', $actionName);
    }

    public function testGetActionNameWithException(): void
    {
        $operation = $this->extractor->getOperationName(new Request([], [], []));
        $this->assertNull($operation);
    }

    protected function setUp(): void
    {
        $this->extractor = new ParametersExtractor(
            '_application',
            '_resource',
            '_operation',
        );
    }
}

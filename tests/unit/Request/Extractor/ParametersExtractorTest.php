<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Request\Extractor;

use LAG\AdminBundle\Request\Extractor\ParametersExtractor;
use LAG\AdminBundle\Request\Extractor\ParametersExtractorInterface;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Request;

final class ParametersExtractorTest extends TestCase
{
    private ParametersExtractorInterface $extractor;

    #[Test]
    public function itReturnsAnApplicationName(): void
    {
        $request = new Request([], [], [
            '_application' => 'my_application',
            '_resource' => 'my_resource',
            '_operation' => 'my_operation',
        ]);
        $applicationName = $this->extractor->getApplicationName($request);

        self::assertEquals('my_application', $applicationName);
    }

    #[Test]
    public function itReturnsAResourceName(): void
    {
        $request = new Request([], [], [
            '_application' => 'my_application',
            '_resource' => 'my_resource',
            '_operation' => 'my_operation',
        ]);
        $resourceName = $this->extractor->getResourceName($request);

        self::assertEquals('my_application.my_resource', $resourceName);
    }

    #[Test]
    public function itReturnsAnOperationName(): void
    {
        $request = new Request([], [], [
            '_application' => 'my_application',
            '_resource' => 'my_resource',
            '_operation' => 'my_operation',
        ]);
        $operationName = $this->extractor->getOperationName($request);

        self::assertEquals('my_application.my_resource.my_operation', $operationName);
    }

    #[Test]
    public function itReturnsAnEmptyName(): void
    {
        $resource = $this->extractor->getResourceName(new Request([], [], [
            '_route_params' => [],
        ]));
        self::assertNull($resource);
    }

    #[Test]
    public function itReturnAnEmptyOperationName(): void
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

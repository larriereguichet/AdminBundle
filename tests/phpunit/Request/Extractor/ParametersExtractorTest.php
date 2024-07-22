<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Request\Extractor;

use LAG\AdminBundle\Request\Extractor\ParametersExtractor;
use LAG\AdminBundle\Request\Extractor\ParametersExtractorInterface;
use LAG\AdminBundle\Tests\ContainerTestTrait;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Request;

final class ParametersExtractorTest extends TestCase
{
    use ContainerTestTrait;

    private ParametersExtractorInterface $extractor;

    #[Test]
    public function itReturnsAResourceName(): void
    {
        $adminName = $this->extractor->getResourceName(new Request([], [], [
            '_resource' => 'my_admin',
            '_operation' => 'my_action',
        ]));

        $this->assertEquals('my_admin', $adminName);
    }

    #[Test]
    public function itReturnsAnEmptyName(): void
    {
        $resource = $this->extractor->getResourceName(new Request([], [], [
            '_route_params' => [],
        ]));
        $this->assertNull($resource);
    }

    #[Test]
    public function itReturnsAnOperationName(): void
    {
        $actionName = $this->extractor->getOperationName(new Request([], [], [
            '_resource' => 'my_admin',
            '_operation' => 'my_action',
        ]));

        $this->assertEquals('my_action', $actionName);
    }

    #[Test]
    public function itReturnAnEmptyOperationName(): void
    {
        $operation = $this->extractor->getOperationName(new Request([], [], []));
        $this->assertNull($operation);
    }

    #[Test]
    public function serviceExists(): void
    {
        self::assertService(ParametersExtractorInterface::class);
        self::assertNoService(ParametersExtractor::class);
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

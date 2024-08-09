<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Functional;

use LAG\AdminBundle\Request\Extractor\ResourceParametersExtractor;
use LAG\AdminBundle\Request\Extractor\ResourceParametersExtractorInterface;
use LAG\AdminBundle\Tests\ContainerTestTrait;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class RequestServicesTest extends TestCase
{
    use ContainerTestTrait;

    #[Test]
    public function serviceExists(): void
    {
        self::assertService(ResourceParametersExtractorInterface::class);
        self::assertNoService(ResourceParametersExtractor::class);
    }
}

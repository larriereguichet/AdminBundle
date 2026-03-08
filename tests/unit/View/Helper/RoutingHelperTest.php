<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\View\Helper;

use LAG\AdminBundle\Routing\UrlGenerator\OperationUrlGeneratorInterface;
use LAG\AdminBundle\Tests\Unit\Fixtures\Book;
use LAG\AdminBundle\Twig\Runtime\RoutingRuntime;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class RoutingHelperTest extends TestCase
{
    private RoutingRuntime $helper;
    private MockObject $urlGenerator;

    #[Test]
    public function itGeneratesAPath(): void
    {
        $data = new Book();

        $this->urlGenerator
            ->expects($this->once())
            ->method('generateFromOperationName')
            ->with('my_resource.my_operation', $data)
            ->willReturn('/url')
        ;

        $url = $this->helper->generatePath('my_resource.my_operation', $data);

        self::assertEquals('/url', $url);
    }

    protected function setUp(): void
    {
        $this->urlGenerator = $this->createMock(OperationUrlGeneratorInterface::class);
        $this->helper = new RoutingRuntime(
            $this->urlGenerator,
        );
    }
}

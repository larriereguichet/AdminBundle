<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\View\Helper;

use LAG\AdminBundle\Resource\Context\ResourceContextInterface;
use LAG\AdminBundle\Resource\Metadata\Resource;
use LAG\AdminBundle\Resource\Metadata\Show;
use LAG\AdminBundle\Routing\UrlGenerator\UrlGeneratorInterface;
use LAG\AdminBundle\Tests\Application\Entity\Book;
use LAG\AdminBundle\View\Helper\RoutingHelper;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class RoutingHelperTest extends TestCase
{
    private RoutingHelper $helper;
    private MockObject $resourceContext;
    private MockObject $requestStack;
    private MockObject $urlGenerator;

    #[Test]
    public function itGeneratesAPath(): void
    {
        $request = new Request();
        $resource = new Resource(name: 'my_resource');
        $operation = (new Show())->withResource($resource);
        $data = new Book();

        $this->requestStack
            ->expects(self::once())
            ->method('getCurrentRequest')
            ->willReturn($request)
        ;
        $this->resourceContext
            ->method('getOperation')
            ->with($request)
            ->willReturn($operation)
        ;
        $this->urlGenerator
            ->expects(self::once())
            ->method('generateFromOperationName')
            ->with('my_resource', 'my_operation', $data)
            ->willReturn('/url')
        ;

        $url = $this->helper->generatePath('my_resource', 'my_operation', $data);

        self::assertEquals('/url', $url);
    }

    protected function setUp(): void
    {
        $this->resourceContext = self::createMock(ResourceContextInterface::class);
        $this->requestStack = self::createMock(RequestStack::class);
        $this->urlGenerator = self::createMock(UrlGeneratorInterface::class);
        $this->helper = new RoutingHelper(
            $this->resourceContext,
            $this->requestStack,
            $this->urlGenerator,
        );
    }
}

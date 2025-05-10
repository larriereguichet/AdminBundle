<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\View\Helper;

use LAG\AdminBundle\Metadata\Resource;
use LAG\AdminBundle\Metadata\Show;
use LAG\AdminBundle\Resource\Context\ResourceContextInterface;
use LAG\AdminBundle\Routing\UrlGenerator\ResourceUrlGeneratorInterface;
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
    private MockObject $urlGenerator;

    #[Test]
    public function itGeneratesAPath(): void
    {
        $data = new Book();

        $this->urlGenerator
            ->expects(self::once())
            ->method('generateFromOperationName')
            ->with('my_resource.my_operation', $data)
            ->willReturn('/url')
        ;

        $url = $this->helper->generatePath('my_resource.my_operation', $data);

        self::assertEquals('/url', $url);
    }

    protected function setUp(): void
    {
        $this->urlGenerator = self::createMock(ResourceUrlGeneratorInterface::class);
        $this->helper = new RoutingHelper(
            $this->urlGenerator,
        );
    }
}

<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Request\Resolver;

use LAG\AdminBundle\LAGAdminBundle;
use LAG\AdminBundle\Request\Resolver\OperationValueResolver;
use LAG\AdminBundle\Resource\Context\ResourceContextInterface;
use LAG\AdminBundle\Resource\Metadata\Resource;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class OperationValueResolverTest extends TestCase
{
    private OperationValueResolver $resolver;
    private MockObject $resourceContext;

    /** @dataProvider typeProvider */
    public function testResolveWrongType(string $type): void
    {
        $request = new Request(['test']);
        $this
            ->resourceContext
            ->expects(self::once())
            ->method('supports')
            ->with($request)
            ->willReturn(true)
        ;

        $parameters = $this->resolver->resolve($request, new ArgumentMetadata('test', $type, false, false, null));
        $parameters = iterator_to_array($parameters);

        $this->assertCount(0, $parameters);
    }

    public function testResolveWithoutSupports(): void
    {
        $request = new Request(['test']);
        $this
            ->resourceContext
            ->expects(self::once())
            ->method('supports')
            ->with($request)
            ->willReturn(false)
        ;

        $parameters = $this->resolver->resolve($request, new ArgumentMetadata('test', null, false, false, null));
        $parameters = iterator_to_array($parameters);

        $this->assertCount(0, $parameters);
    }

    public static function typeProvider(): array
    {
        return [
            ['string'],
            ['int'],
            ['bool'],
            [Resource::class],
            [LAGAdminBundle::class],
        ];
    }

    protected function setUp(): void
    {
        $this->resourceContext = self::createMock(ResourceContextInterface::class);
        $this->resolver = new OperationValueResolver($this->resourceContext);
    }
}

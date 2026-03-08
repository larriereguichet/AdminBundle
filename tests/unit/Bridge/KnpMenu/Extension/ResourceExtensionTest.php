<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Bridge\KnpMenu\Extension;

use LAG\AdminBundle\Bridge\KnpMenu\Extension\ResourceExtension;
use LAG\AdminBundle\Metadata\Attribute\Resource;
use LAG\AdminBundle\Metadata\Attribute\Show;
use LAG\AdminBundle\Metadata\Factory\OperationFactoryInterface;
use LAG\AdminBundle\Routing\UrlGenerator\OperationUrlGeneratorInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class ResourceExtensionTest extends TestCase
{
    private ResourceExtension $extension;
    private MockObject $operationFactory;
    private MockObject $urlGenerator;

    #[Test]
    public function itBuildOptions(): void
    {
        $operation = new Show(name: 'my_operation', route: 'my_route', title: 'Some title');
        $resource = new Resource(
            shortName: 'my_resource',
            operations: [$operation],
            translationDomain: 'my_domain',
        );
        $operation = $operation->setResource($resource);

        $this->operationFactory
            ->expects($this->once())
            ->method('create')
            ->with('my_operation')
            ->willReturn($operation)
        ;
        $this->urlGenerator
            ->expects($this->once())
            ->method('generateUrl')
            ->with($operation)
            ->willReturn('/some-url')
        ;

        $options = [
            'some_option' => 'some_value',
            'resource' => 'my_resource',
            'operation' => 'my_operation',
        ];
        $buildOptions = $this->extension->buildOptions($options);

        self::assertEquals($options + [
            'uri' => '/some-url',
            'label' => 'Some title',
            'extras' => ['translation_domain' => 'my_domain'],
        ], $buildOptions);
    }

    #[Test]
    public function itDoesNotBuildOptionsWithoutResource(): void
    {
        $this->operationFactory
            ->expects($this->never())
            ->method('create')
        ;
        $options = ['some_option' => 'some_value'];
        $buildOptions = $this->extension->buildOptions($options);

        self::assertEquals($options, $buildOptions);
    }

    protected function setUp(): void
    {
        $this->operationFactory = $this->createMock(OperationFactoryInterface::class);
        $this->urlGenerator = $this->createMock(OperationUrlGeneratorInterface::class);
        $this->extension = new ResourceExtension($this->operationFactory, $this->urlGenerator);
    }
}

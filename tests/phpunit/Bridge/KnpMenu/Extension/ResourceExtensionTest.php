<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Bridge\KnpMenu\Extension;

use LAG\AdminBundle\Bridge\KnpMenu\Extension\ResourceExtension;
use LAG\AdminBundle\Resource\Metadata\Get;
use LAG\AdminBundle\Resource\Metadata\Resource;
use LAG\AdminBundle\Resource\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Routing\UrlGenerator\UrlGeneratorInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class ResourceExtensionTest extends TestCase
{
    private ResourceExtension $extension;
    private MockObject $registry;
    private MockObject $urlGenerator;

    #[Test]
    public function itBuildOptions(): void
    {
        $operation = new Get(name: 'my_operation', route: 'my_route', title: 'Some title');
        $resource = new Resource(
            name: 'my_resource',
            operations: [$operation],
            translationDomain: 'my_domain',
        );

        $this->registry
            ->expects(self::once())
            ->method('has')
            ->with('my_resource')
            ->willReturn(true)
        ;
        $this->registry
            ->expects(self::once())
            ->method('get')
            ->with('my_resource')
            ->willReturn($resource)
        ;
        $this->urlGenerator
            ->expects(self::once())
            ->method('generateOperationUrl')
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
    public function itDoesNotBuildOptionsMissingOperation(): void
    {
        $resource = new Resource(name: 'my_resource');

        $this->registry
            ->expects(self::once())
            ->method('has')
            ->with('my_resource')
            ->willReturn(true)
        ;
        $this->registry
            ->expects(self::once())
            ->method('get')
            ->with('my_resource')
            ->willReturn($resource)
        ;

        $options = [
            'some_option' => 'some_value',
            'resource' => 'my_resource',
            'operation' => 'my_operation',
        ];
        $buildOptions = $this->extension->buildOptions($options);

        self::assertEquals($options, $buildOptions);
    }

    #[Test]
    public function itDoesNotBuildOptionsMissingResource(): void
    {
        $this->registry
            ->expects(self::once())
            ->method('has')
            ->with('my_resource')
            ->willReturn(false)
        ;
        $options = [
            'some_option' => 'some_value',
            'resource' => 'my_resource',
            'operation' => 'my_operation',
        ];
        $buildOptions = $this->extension->buildOptions($options);

        self::assertEquals($options, $buildOptions);
    }

    #[Test]
    public function itDoesNotBuildOptionsWithoutResource(): void
    {
        $this->registry
            ->expects(self::never())
            ->method('has')
        ;
        $options = ['some_option' => 'some_value'];
        $buildOptions = $this->extension->buildOptions($options);

        self::assertEquals($options, $buildOptions);
    }

    protected function setUp(): void
    {
        $this->registry = self::createMock(ResourceRegistryInterface::class);
        $this->urlGenerator = self::createMock(UrlGeneratorInterface::class);
        $this->extension = new ResourceExtension($this->registry, $this->urlGenerator);
    }
}

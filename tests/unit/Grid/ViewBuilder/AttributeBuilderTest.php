<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Grid\ViewBuilder;

use LAG\AdminBundle\Grid\ViewBuilder\AttributeBuilder;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Runtime\EscaperRuntime;

final class AttributeBuilderTest extends TestCase
{
    private AttributeBuilder $attributeBuilder;
    private MockObject $environment;

    #[Test]
    public function itBuildAttributes(): void
    {
        $this->environment
            ->expects($this->once())
            ->method('getRuntime')
            ->with(EscaperRuntime::class)
            ->willReturn(new EscaperRuntime())
        ;
        $attributes = $this->attributeBuilder->buildAttributes(['some_attribute' => 'some_value']);

        $this->assertEquals(['some_attribute' => 'some_value'], $attributes->all());
    }

    protected function setUp(): void
    {
        $this->environment = $this->createMock(Environment::class);
        $this->attributeBuilder = new AttributeBuilder($this->environment);
    }
}

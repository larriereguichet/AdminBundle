<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Grid\ViewBuilder;

use LAG\AdminBundle\Grid\View\CellView;
use LAG\AdminBundle\Grid\ViewBuilder\AttributeBuilderInterface;
use LAG\AdminBundle\Grid\ViewBuilder\CellBuilderInterface;
use LAG\AdminBundle\Grid\ViewBuilder\SecurityCellBuilder;
use LAG\AdminBundle\Metadata\Attribute\Grid;
use LAG\AdminBundle\Metadata\Attribute\Index;
use LAG\AdminBundle\Metadata\Attribute\Text;
use LAG\AdminBundle\Security\PermissionChecker\PropertyPermissionCheckerInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\UX\TwigComponent\ComponentAttributes;
use Twig\Runtime\EscaperRuntime;

final class SecurityViewBuilderTest extends TestCase
{
    private SecurityCellBuilder $cellBuilder;
    private MockObject $permissionChecker;
    private MockObject $decorated;
    private MockObject $attributeBuilder;

    #[Test]
    public function itCreateAuthorizedProperty(): void
    {
        $grid = new Grid(name: 'some grid');
        $property = new Text(name: 'some property', permissions: ['ROLE_USER']);
        $data = new \stdClass();
        $context = ['some_context'];
        $attributes = new ComponentAttributes([], new EscaperRuntime());
        $cellView = new CellView(name: 'some property', attributes: $attributes);
        $operation = new Index();

        $this->attributeBuilder
            ->expects($this->never())
            ->method('buildAttributes')
        ;
        $this->permissionChecker
            ->expects($this->once())
            ->method('isGranted')
            ->with($property)
            ->willReturn(true)
        ;
        $this->decorated
            ->expects($this->once())
            ->method('buildCell')
            ->with($operation, $grid, $property, $data, $context)
            ->willReturn($cellView)
        ;
        $result = $this->cellBuilder->buildCell($operation, $grid, $property, $data, $context);

        self::assertEquals($cellView, $result);
    }

    #[Test]
    public function itDoesNotCreateUnauthorizedProperty(): void
    {
        $grid = new Grid(name: 'some grid');
        $property = new Text(name: 'some property', permissions: ['ROLE_USER']);
        $operation = new Index();

        $emptyAttributes = new ComponentAttributes([], new EscaperRuntime());

        $this->permissionChecker
            ->expects($this->once())
            ->method('isGranted')
            ->with($property)
            ->willReturn(false)
        ;
        $this->attributeBuilder
            ->expects($this->once())
            ->method('buildAttributes')
            ->willReturn($emptyAttributes)
        ;
        $this->decorated
            ->expects($this->never())
            ->method('buildCell')
        ;
        $cellView = $this->cellBuilder->buildCell($operation, $grid, $property, new \stdClass());

        self::assertEquals($property->getName(), $cellView->name);
        self::assertNull($cellView->template);
    }

    protected function setUp(): void
    {
        $this->decorated = $this->createMock(CellBuilderInterface::class);
        $this->permissionChecker = $this->createMock(PropertyPermissionCheckerInterface::class);
        $this->attributeBuilder = $this->createMock(AttributeBuilderInterface::class);
        $this->cellBuilder = new SecurityCellBuilder(
            $this->decorated,
            $this->permissionChecker,
            $this->attributeBuilder,
        );
    }
}

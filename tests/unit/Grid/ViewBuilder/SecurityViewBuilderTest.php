<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Grid\ViewBuilder;

use LAG\AdminBundle\Grid\View\CellView;
use LAG\AdminBundle\Grid\ViewBuilder\CellViewBuilderInterface;
use LAG\AdminBundle\Grid\ViewBuilder\SecurityCellViewBuilder;
use LAG\AdminBundle\Metadata\Grid;
use LAG\AdminBundle\Metadata\Index;
use LAG\AdminBundle\Metadata\Text;
use LAG\AdminBundle\Security\PermissionChecker\PropertyPermissionCheckerInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class SecurityViewBuilderTest extends TestCase
{
    private SecurityCellViewBuilder $cellBuilder;
    private MockObject $permissionChecker;
    private MockObject $decorated;

    #[Test]
    public function itCreateAuthorizedProperty(): void
    {
        $grid = new Grid(name: 'some grid');
        $property = new Text(name: 'some property', permissions: ['ROLE_USER']);
        $data = new \stdClass();
        $context = ['some_context'];
        $cellView = new CellView(name: 'some property');
        $operation = new Index();

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

        $this->permissionChecker
            ->expects($this->once())
            ->method('isGranted')
            ->with($property)
            ->willReturn(false)
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
        $this->decorated = $this->createMock(CellViewBuilderInterface::class);
        $this->permissionChecker = $this->createMock(PropertyPermissionCheckerInterface::class);
        $this->cellBuilder = new SecurityCellViewBuilder(
            $this->decorated,
            $this->permissionChecker,
        );
    }
}

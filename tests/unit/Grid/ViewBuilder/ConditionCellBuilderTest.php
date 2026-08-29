<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Grid\ViewBuilder;

use LAG\AdminBundle\Condition\Matcher\ConditionMatcherInterface;
use LAG\AdminBundle\Grid\View\CellView;
use LAG\AdminBundle\Grid\ViewBuilder\AttributeBuilderInterface;
use LAG\AdminBundle\Grid\ViewBuilder\CellBuilderInterface;
use LAG\AdminBundle\Grid\ViewBuilder\ConditionCellBuilder;
use LAG\AdminBundle\Metadata\Attribute\Grid;
use LAG\AdminBundle\Metadata\Attribute\Text;
use LAG\AdminBundle\Metadata\Attribute\Update;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\UX\TwigComponent\ComponentAttributes;
use Twig\Runtime\EscaperRuntime;

final class ConditionCellBuilderTest extends TestCase
{
    private ConditionCellBuilder $builder;
    private MockObject $decorated;
    private MockObject $conditionMatcher;

    #[Test]
    public function itBuildsAPropertyWithoutCondition(): void
    {
        $property = new Text(name: 'email');
        $cellView = $this->createCellView('email');

        $this->conditionMatcher
            ->expects($this->never())
            ->method('matchCondition')
        ;
        $this->decorated
            ->expects($this->once())
            ->method('buildCell')
            ->willReturn($cellView)
        ;

        self::assertSame($cellView, $this->build($property));
    }

    #[Test]
    public function itBuildsAPropertyWhenTheConditionMatches(): void
    {
        $property = new Text(name: 'email', condition: 'resource.hasUser()');
        $cellView = $this->createCellView('email');

        $this->conditionMatcher
            ->expects($this->once())
            ->method('matchCondition')
            ->willReturn(true)
        ;
        $this->decorated
            ->expects($this->once())
            ->method('buildCell')
            ->willReturn($cellView)
        ;

        self::assertSame($cellView, $this->build($property));
    }

    /**
     * A condition that does not match has to short circuit the rest of the chain: the property path is
     * never read, so a condition can guard a path that is not readable on every row.
     */
    #[Test]
    public function itDoesNotBuildAPropertyWhenTheConditionDoesNotMatch(): void
    {
        $property = new Text(name: 'email', propertyPath: 'user.email', condition: 'resource.hasUser()');

        $this->conditionMatcher
            ->expects($this->once())
            ->method('matchCondition')
            ->willReturn(false)
        ;
        $this->decorated
            ->expects($this->never())
            ->method('buildCell')
        ;

        $cellView = $this->build($property);

        self::assertSame('email', $cellView->name);
        self::assertNull($cellView->data);
        self::assertNull($cellView->property);
    }

    protected function setUp(): void
    {
        $this->decorated = $this->createMock(CellBuilderInterface::class);
        $this->conditionMatcher = $this->createMock(ConditionMatcherInterface::class);
        $attributeBuilder = $this->createStub(AttributeBuilderInterface::class);
        $attributeBuilder
            ->method('buildAttributes')
            ->willReturn(new ComponentAttributes([], new EscaperRuntime()))
        ;
        $this->builder = new ConditionCellBuilder(
            $this->conditionMatcher,
            $this->decorated,
            $attributeBuilder,
        );
    }

    private function build(Text $property): CellView
    {
        $data = new \stdClass();

        return $this->builder->buildCell(
            new Update(),
            new Grid(name: 'some_grid'),
            $property,
            $data,
            ['row_data' => $data],
        );
    }

    private function createCellView(string $name): CellView
    {
        return new CellView(name: $name, attributes: new ComponentAttributes([], new EscaperRuntime()));
    }
}

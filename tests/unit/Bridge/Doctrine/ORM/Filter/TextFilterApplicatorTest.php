<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Bridge\Doctrine\ORM\Filter;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Filter\TextFilterApplicator;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Metadata\Attribute\TextFilter;
use LAG\AdminBundle\Metadata\FilterInterface;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Metadata\ResourceInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class TextFilterApplicatorTest extends TestCase
{
    private TextFilterApplicator $applicator;
    private Registry $registry;

    #[Test]
    public function itReturnsEarlyWhenPropertiesIsNull(): void
    {
        $filter = new TextFilter('search');
        $qb = $this->createMock(QueryBuilder::class);
        $qb->expects($this->never())->method('leftJoin');
        $qb->expects($this->never())->method('andWhere');

        $this->applicator->apply($this->makeOperation(), $filter, $qb, 'foo');
    }

    #[Test]
    public function itAppliesEqualsFilterOnDirectProperty(): void
    {
        $filter = new TextFilter('search', 'equals', 'and', TextType::class, [], ['title']);
        $qb = $this->buildQueryBuilder('book');

        $qb->expects($this->never())->method('leftJoin');
        $qb->expects($this->once())->method('andWhere')->with('book.title = :filter_search')->willReturnSelf();
        $qb->expects($this->once())->method('setParameter')->with('filter_search', 'foo')->willReturnSelf();

        $this->applicator->apply($this->makeOperation(), $filter, $qb, 'foo');
    }

    #[Test]
    public function itAddsLeftJoinForSingleLevelPath(): void
    {
        $filter = new TextFilter('search', 'equals', 'and', TextType::class, [], ['author.name']);
        $qb = $this->buildQueryBuilder('book');

        $qb->expects($this->once())->method('leftJoin')->with('book.author', 'lag_filter_author')->willReturnSelf();
        $qb->expects($this->once())->method('andWhere')->with('lag_filter_author.name = :filter_search')->willReturnSelf();
        $qb->expects($this->once())->method('setParameter')->with('filter_search', 'bar')->willReturnSelf();

        $this->applicator->apply($this->makeOperation(), $filter, $qb, 'bar');
    }

    #[Test]
    public function itAddsNestedLeftJoinsForMultiLevelPath(): void
    {
        $filter = new TextFilter('search', 'equals', 'and', TextType::class, [], ['author.publisher.name']);
        $qb = $this->buildQueryBuilder('book');

        $qb->expects($this->exactly(2))->method('leftJoin')
            ->willReturnCallback(static function (string $join, string $alias) use ($qb): QueryBuilder {
                static $calls = [];
                $calls[] = [$join, $alias];
                if (\count($calls) === 1) {
                    self::assertSame('book.author', $join);
                    self::assertSame('lag_filter_author', $alias);
                } else {
                    self::assertSame('lag_filter_author.publisher', $join);
                    self::assertSame('lag_filter_author_publisher', $alias);
                }

                return $qb;
            });
        $qb->expects($this->once())->method('andWhere')->with('lag_filter_author_publisher.name = :filter_search')->willReturnSelf();
        $qb->expects($this->once())->method('setParameter')->willReturnSelf();

        $this->applicator->apply($this->makeOperation(), $filter, $qb, 'baz');
    }

    #[Test]
    public function itDeduplicatesJoinsWhenMultiplePropertiesShareTheSamePath(): void
    {
        $filter = new TextFilter('search', 'equals', 'and', TextType::class, [], ['author.name', 'author.email']);
        $qb = $this->buildQueryBuilder('book');
        $qb->method('getAllAliases')->willReturnOnConsecutiveCalls(['book'], ['book', 'lag_filter_author']);

        // One join, two andWhere calls (one per field), one setParameter (shared param)
        $qb->expects($this->once())->method('leftJoin')->with('book.author', 'lag_filter_author')->willReturnSelf();
        $qb->expects($this->exactly(2))->method('andWhere')->willReturnSelf();
        $qb->expects($this->once())->method('setParameter')->willReturnSelf();

        $this->applicator->apply($this->makeOperation(), $filter, $qb, 'test');
    }

    #[Test]
    public function itReusesAJoinAddedByAnotherFilterOnTheSameQueryBuilder(): void
    {
        $qb = $this->buildQueryBuilder('book');
        $qb->method('getAllAliases')->willReturnOnConsecutiveCalls(['book'], ['book', 'lag_filter_author']);

        // Every filter of an operation is applied to the same query builder: joining twice under the same alias
        // makes Doctrine throw
        $qb->expects($this->once())->method('leftJoin')->with('book.author', 'lag_filter_author')->willReturnSelf();
        $qb->expects($this->exactly(2))->method('andWhere')->willReturnSelf();
        $qb->expects($this->exactly(2))->method('setParameter')->willReturnSelf();

        $operation = $this->makeOperation();
        $name = new TextFilter('name', 'equals', 'and', TextType::class, [], ['author.name']);
        $email = new TextFilter('email', 'equals', 'and', TextType::class, [], ['author.email']);

        $this->applicator->apply($operation, $name, $qb, 'foo');
        $this->applicator->apply($operation, $email, $qb, 'bar');
    }

    #[Test]
    public function itHandlesMixOfDirectAndJoinedProperties(): void
    {
        $filter = new TextFilter('search', 'equals', 'and', TextType::class, [], ['title', 'author.name']);
        $qb = $this->buildQueryBuilder('book');

        // One join for 'author.name', two andWhere (one per group), two setParameter (one per group)
        $qb->expects($this->once())->method('leftJoin')->with('book.author', 'lag_filter_author')->willReturnSelf();
        $qb->expects($this->exactly(2))->method('andWhere')->willReturnSelf();
        $qb->expects($this->exactly(2))->method('setParameter')->willReturnSelf();

        $this->applicator->apply($this->makeOperation(), $filter, $qb, 'test');
    }

    #[Test]
    public function itAppliesLikeFilterWithJoin(): void
    {
        $filter = new TextFilter('search', 'like', 'and', TextType::class, [], ['author.name']);
        $qb = $this->buildQueryBuilder('book');
        $qb->method('expr')->willReturn(new Expr());

        $qb->expects($this->once())->method('leftJoin')->with('book.author', 'lag_filter_author')->willReturnSelf();
        $qb->expects($this->once())->method('andWhere')->willReturnSelf();
        $qb->expects($this->once())->method('setParameter')->with('filter_search', '%foo%')->willReturnSelf();

        $this->applicator->apply($this->makeOperation(), $filter, $qb, 'foo');
    }

    #[Test]
    public function itSupportsTextFilterWithQueryBuilder(): void
    {
        $filter = new TextFilter('search');
        $qb = $this->createStub(QueryBuilder::class);

        self::assertTrue($this->applicator->supports($this->makeOperation(), $filter, $qb, 'foo'));
    }

    #[Test]
    public function itDoesNotSupportNonTextFilter(): void
    {
        $filter = $this->createStub(FilterInterface::class);
        $qb = $this->createStub(QueryBuilder::class);

        self::assertFalse($this->applicator->supports($this->makeOperation(), $filter, $qb, 'foo'));
    }

    #[Test]
    public function itDoesNotSupportNonQueryBuilderData(): void
    {
        $filter = new TextFilter('search');

        self::assertFalse($this->applicator->supports($this->makeOperation(), $filter, new \stdClass(), 'foo'));
    }

    #[Test]
    public function itThrowsOnBetweenFilterWithNonArrayValue(): void
    {
        $filter = new TextFilter('search', 'between', 'and', TextType::class, [], ['title']);
        $qb = $this->buildQueryBuilder('book');
        $qb->method('expr')->willReturn(new Expr());

        $this->expectException(Exception::class);

        $this->applicator->apply($this->makeOperation(), $filter, $qb, 'not_an_array');
    }

    #[Test]
    public function itThrowsOnBetweenFilterWithExactlyTwoValues(): void
    {
        $filter = new TextFilter('search', 'between', 'and', TextType::class, [], ['title']);
        $qb = $this->buildQueryBuilder('book');
        $qb->method('expr')->willReturn(new Expr());

        $this->expectException(Exception::class);

        $this->applicator->apply($this->makeOperation(), $filter, $qb, ['2024-01-01', '2024-12-31']);
    }

    #[Test]
    public function itAppliesBetweenFilterWithThreeValues(): void
    {
        $filter = new TextFilter('search', 'between', 'and', TextType::class, [], ['title']);
        $qb = $this->buildQueryBuilder('book');
        $qb->method('expr')->willReturn(new Expr());
        $qb->expects($this->once())->method('andWhere')->willReturnSelf();
        $qb->expects($this->exactly(2))->method('setParameter')->willReturnSelf();

        $this->applicator->apply($this->makeOperation(), $filter, $qb, ['2024-01-01', '2024-06-30', '2024-12-31']);
    }

    protected function setUp(): void
    {
        $this->registry = $this->createStub(Registry::class);
        $this->registry->method('getManagerForClass')->willReturn($this->createStub(EntityManagerInterface::class));
        $this->applicator = new TextFilterApplicator($this->registry);
    }

    private function buildQueryBuilder(string $rootAlias): MockObject
    {
        $qb = $this->createMock(QueryBuilder::class);
        $qb->expects($this->any())->method('getRootAliases')->willReturn([$rootAlias]);

        return $qb;
    }

    private function makeOperation(): OperationInterface
    {
        $resource = $this->createStub(ResourceInterface::class);
        $resource->method('getResourceClass')->willReturn('App\Entity\Book');
        $operation = $this->createStub(OperationInterface::class);
        $operation->method('getResource')->willReturn($resource);

        return $operation;
    }
}

<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Bridge\Doctrine\ORM\EventListener;

use LAG\AdminBundle\Bridge\Doctrine\ORM\EventListener\OperationCreateListener;
use LAG\AdminBundle\Event\OperationEvent;
use LAG\AdminBundle\Filter\Factory\FilterFactoryInterface;
use LAG\AdminBundle\Resource\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Resource\Metadata\Filter;
use LAG\AdminBundle\Resource\Metadata\Index;
use LAG\AdminBundle\Resource\Metadata\Text;
use LAG\AdminBundle\Resource\Metadata\TextFilter;
use LAG\AdminBundle\Resource\Metadata\Update;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class OperationCreateListenerTest extends TestCase
{
    private OperationCreateListener $listener;
    private MockObject $filterFactory;

    public function testInvoke(): void
    {
        $operation = new Index();
        $operation = $operation
            ->withProperties([new Text('a_property')])
            ->withIdentifiers(['a_property'])
        ;

        $filter = new Filter('a_property');

        $this
            ->filterFactory
            ->expects($this->once())
            ->method('createFromProperty')
            ->willReturn($filter)
        ;

        $this->listener->__invoke($event = new OperationEvent($operation));
        /** @var CollectionOperationInterface $operation */
        $operation = $event->getOperation();
        $this->assertCount(1, $operation->getFilters());

        $filter = $operation->getFilters()[0];
        $this->assertEquals('a_property', $filter->getName());
        $this->assertEquals('=', $filter->getComparator());
    }

    public function testInvokeWithoutCollectionOperation(): void
    {
        $operation = $this->createMock(Update::class);
        $operation
            ->expects($this->never())
            ->method('getProperties')
        ;

        $this->listener->__invoke(new OperationEvent($operation));
    }

    public function testInvokeWithoutProperties(): void
    {
        $operation = new Index();
        $this
            ->filterFactory
            ->expects($this->never())
            ->method('createFromProperty')
        ;

        $this->listener->__invoke(new OperationEvent($operation));
    }

    public function testInvokeWithFilters(): void
    {
        $operation = new Index();
        $operation = $operation
            ->withFilters([new TextFilter('a_property')])
            ->withProperties([new Text('a_property')])
        ;
        $this
            ->filterFactory
            ->expects($this->never())
            ->method('createFromProperty')
        ;

        $this->listener->__invoke(new OperationEvent($operation));
    }

    public function testService(): void
    {
        $this->assertServiceExists(OperationCreateListener::class);
    }

    protected function setUp(): void
    {
        $this->filterFactory = $this->createMock(FilterFactoryInterface::class);
        $this->listener = new OperationCreateListener($this->filterFactory);
    }
}

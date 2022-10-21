<?php

namespace LAG\AdminBundle\Tests\Bridge\Doctrine\ORM\EventListener;

use LAG\AdminBundle\Bridge\Doctrine\ORM\EventListener\OperationCreateListener;
use LAG\AdminBundle\Event\Events\OperationCreateEvent;
use LAG\AdminBundle\Filter\Factory\FilterFactoryInterface;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\Filter\Filter;
use LAG\AdminBundle\Metadata\Filter\StringFilter;
use LAG\AdminBundle\Metadata\Index;
use LAG\AdminBundle\Metadata\Property\StringProperty;
use LAG\AdminBundle\Metadata\Update;
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
            ->withProperties([new StringProperty('a_property')])
            ->withIdentifiers(['a_property'])
        ;

        $filter = new Filter('a_property');

        $this
            ->filterFactory
            ->expects($this->once())
            ->method('createFromProperty')
            ->willReturn($filter)
        ;

        $this->listener->__invoke($event = new OperationCreateEvent($operation));
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

        $this->listener->__invoke(new OperationCreateEvent($operation));
    }

    public function testInvokeWithoutProperties(): void
    {
        $operation = new Index();
        $this
            ->filterFactory
            ->expects($this->never())
            ->method('createFromProperty')
        ;

        $this->listener->__invoke(new OperationCreateEvent($operation));
    }

    public function testInvokeWithFilters(): void
    {
        $operation = new Index();
        $operation = $operation
            ->withFilters([new StringFilter('a_property')])
            ->withProperties([new StringProperty('a_property')])
        ;
        $this
            ->filterFactory
            ->expects($this->never())
            ->method('createFromProperty')
        ;

        $this->listener->__invoke(new OperationCreateEvent($operation));
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

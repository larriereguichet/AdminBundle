<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Request\ContextBuilder;

use LAG\AdminBundle\Metadata\Create;
use LAG\AdminBundle\Metadata\Delete;
use LAG\AdminBundle\Metadata\Index;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Metadata\Show;
use LAG\AdminBundle\Metadata\Update;
use LAG\AdminBundle\Request\ContextBuilder\FilterContextBuilder;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

final class FilterContextBuilderTest extends TestCase
{
    private FilterContextBuilder $provider;
    private MockObject $formFactory;

    #[Test]
    public function itAddsFilteringContext(): void
    {
        $request = new Request();
        $operation = new Index()
            ->withFilterForm('SomeFormType')
            ->withFilterFormOptions([])
        ;
        $form = self::createMock(FormInterface::class);

        $this->formFactory
            ->expects(self::once())
            ->method('create')
            ->with('SomeFormType')
            ->willReturn($form)
        ;
        $form->expects(self::once())
            ->method('handleRequest')
            ->with($request)
        ;
        $form->expects(self::once())
            ->method('isSubmitted')
            ->willReturn(true)
        ;
        $form->expects(self::once())
            ->method('isValid')
            ->willReturn(true)
        ;
        $form->expects(self::once())
            ->method('getData')
            ->willReturn(['my_filter' => 'my_value'])
        ;

        $context = $this->provider->buildContext($operation, $request);

        self::assertEquals('my_value', $context['filters']['my_filter']);
    }

    #[Test]
    #[DataProvider(methodName: 'nonCollectionOperations')]
    public function itDoesNotAddContextOnNonCollectionOperation(OperationInterface $operation): void
    {
        $request = new Request();

        $this->formFactory
            ->expects(self::never())
            ->method('create')
        ;
        self::assertFalse($this->provider->supports($operation, $request));
    }

    public static function nonCollectionOperations(): iterable
    {
        yield [new Create()];
        yield [new Update()];
        yield [new Delete()];
        yield [new Show()];
    }

    protected function setUp(): void
    {
        $this->formFactory = self::createMock(FormFactoryInterface::class);
        $this->provider = new FilterContextBuilder(
            $this->formFactory,
        );
    }
}

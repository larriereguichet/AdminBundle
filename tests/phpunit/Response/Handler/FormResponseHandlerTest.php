<?php

namespace LAG\AdminBundle\Tests\Response\Handler;

use LAG\AdminBundle\Metadata\Create;
use LAG\AdminBundle\Response\Handler\FormResponseHandler;
use LAG\AdminBundle\Response\Handler\ResponseHandlerInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class FormResponseHandlerTest extends TestCase
{
    private FormResponseHandler $handler;
    private MockObject $responseHandler;

    #[Test]
    public function itReturnsAFormResponse(): void
    {
        $operation = new Create();
        $data = new stdClass();
        $request = new Request();

        $formView = self::createMock(FormView::class);
        $form = self::createMock(FormInterface::class);
        $form->expects(self::once())
            ->method('createView')
            ->willReturn($formView)
        ;
        $form->expects(self::once())
            ->method('isSubmitted')
            ->willReturn(true)
        ;
        $form->expects(self::once())
            ->method('isValid')
            ->willReturn(false)
        ;

        $customFormView = self::createMock(FormView::class);
        $customForm = self::createMock(FormInterface::class);
        $customForm->expects(self::once())
            ->method('createView')
            ->willReturn($customFormView)
        ;

        $context = [
            'form' => $form,
            'customForm' => $customForm,
            'some_data' => 'some_value',
        ];

        $this->responseHandler
            ->expects(self::once())
            ->method('createResponse')
            ->with($operation, $data, $request, [
                'form' => $formView,
                'customForm' => $customFormView,
                'some_data' => 'some_value',
                'responseCode' => Response::HTTP_UNPROCESSABLE_ENTITY,
            ])
            ->willReturn(new Response('some html content'))
        ;

        $this->handler->createResponse($operation, $data, $request, $context);
    }

    protected function setUp(): void
    {
        $this->responseHandler = self::createMock(ResponseHandlerInterface::class);
        $this->handler = new FormResponseHandler($this->responseHandler);
    }
}

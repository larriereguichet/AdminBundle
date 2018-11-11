<?php

namespace LAG\AdminBundle\Tests\Event\Subscriber;

use LAG\AdminBundle\Event\Subscriber\FormSubscriber;
use LAG\AdminBundle\Factory\DataProviderFactory;
use LAG\AdminBundle\Tests\AdminTestBase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Translation\TranslatorInterface;

class FormSubscriberTest extends AdminTestBase
{
    public function testServiceExists()
    {
        $this->assertServiceExists(FormSubscriber::class);
    }

    public function testMethodsExists()
    {
        list($subscriber) = $this->createSubscriber();

        $this->assertSubscribedMethodsExists($subscriber);
    }

    private function createSubscriber()
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $dataProviderFactory = $this->createMock(DataProviderFactory::class);
        $session = $this->createMock(Session::class);
        $translator = $this->createMock(TranslatorInterface::class);

        $subscriber = new FormSubscriber($formFactory, $dataProviderFactory, $session, $translator);

        return [
            $subscriber,
            $formFactory,
            $dataProviderFactory
        ];
    }
}

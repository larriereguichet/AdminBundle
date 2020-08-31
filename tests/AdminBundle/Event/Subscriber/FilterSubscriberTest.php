<?php

namespace LAG\AdminBundle\Tests\Event\Subscriber;

use LAG\AdminBundle\Event\Subscriber\FilterSubscriber;
use LAG\AdminBundle\Tests\AdminTestBase;
use LAG\AdminBundle\Translation\Helper\TranslationHelperInterface;
use Symfony\Component\Form\FormFactoryInterface;

class FilterSubscriberTest extends AdminTestBase
{
    public function testServiceExists()
    {
        $this->assertServiceExists(FilterSubscriber::class);
    }

    public function testMethodExists()
    {
        list($subscriber) = $this->createSubscriber();

        $this->assertSubscribedMethodsExists($subscriber);
    }

    private function createSubscriber()
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $translationHelper = $this->createMock(TranslationHelperInterface::class);

        $subscriber = new FilterSubscriber($formFactory, $translationHelper);

        return [
            $subscriber,
            $formFactory,
        ];
    }
}

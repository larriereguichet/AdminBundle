<?php

namespace LAG\AdminBundle\Tests\Utils;

use DG\BypassFinals;
use Throwable;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestListener;
use PHPUnit\Framework\TestSuite;
use PHPUnit\Framework\Warning;

class FinalClassListener implements TestListener
{
    /**
     * @inheritDoc
     */
    public function addError(Test $test, Throwable $e, $time): void
    {
    }

    /**
     * @inheritDoc
     */
    public function addWarning(Test $test, Warning $e, $time): void
    {
    }

    /**
     * @inheritDoc
     */
    public function addFailure(Test $test, AssertionFailedError $e, $time): void
    {
    }

    /**
     * @inheritDoc
     */
    public function addIncompleteTest(Test $test, Throwable $e, $time): void
    {
    }

    /**
     * @inheritDoc
     */
    public function addRiskyTest(Test $test, Throwable $e, $time): void
    {
    }

    /**
     * @inheritDoc
     */
    public function addSkippedTest(Test $test, Throwable $e, $time): void
    {
    }

    /**
     * @inheritDoc
     */
    public function startTestSuite(TestSuite $suite): void
    {
    }

    /**
     * @inheritDoc
     */
    public function endTestSuite(TestSuite $suite): void
    {
    }

    /**
     * @inheritDoc
     */
    public function startTest(Test $test): void
    {
        BypassFinals::enable();
    }

    /**
     * @inheritDoc
     */
    public function endTest(Test $test, $time): void
    {
        // TODO: Implement endTest() method.
    }
}

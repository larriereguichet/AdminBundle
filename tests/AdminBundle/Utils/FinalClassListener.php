<?php

namespace LAG\AdminBundle\Tests\Utils;

use DG\BypassFinals;
use Exception;
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
    public function addError(Test $test, Exception $e, $time)
    {
    }

    /**
     * @inheritDoc
     */
    public function addWarning(Test $test, Warning $e, $time)
    {
    }

    /**
     * @inheritDoc
     */
    public function addFailure(Test $test, AssertionFailedError $e, $time)
    {
    }

    /**
     * @inheritDoc
     */
    public function addIncompleteTest(Test $test, Exception $e, $time)
    {
    }

    /**
     * @inheritDoc
     */
    public function addRiskyTest(Test $test, Exception $e, $time)
    {
    }

    /**
     * @inheritDoc
     */
    public function addSkippedTest(Test $test, Exception $e, $time)
    {
    }

    /**
     * @inheritDoc
     */
    public function startTestSuite(TestSuite $suite)
    {
    }

    /**
     * @inheritDoc
     */
    public function endTestSuite(TestSuite $suite)
    {
    }

    /**
     * @inheritDoc
     */
    public function startTest(Test $test)
    {
        BypassFinals::enable();
    }

    /**
     * @inheritDoc
     */
    public function endTest(Test $test, $time)
    {
        // TODO: Implement endTest() method.
    }
}

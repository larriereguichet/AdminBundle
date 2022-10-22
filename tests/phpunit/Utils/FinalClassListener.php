<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Utils;

use DG\BypassFinals;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestListener;
use PHPUnit\Framework\TestSuite;
use PHPUnit\Framework\Warning;
use Throwable;

class FinalClassListener implements TestListener
{
    /**
     * {@inheritdoc}
     */
    public function addError(Test $test, Throwable $e, $time): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function addWarning(Test $test, Warning $e, $time): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function addFailure(Test $test, AssertionFailedError $e, $time): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function addIncompleteTest(Test $test, Throwable $e, $time): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function addRiskyTest(Test $test, Throwable $e, $time): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function addSkippedTest(Test $test, Throwable $e, $time): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function startTestSuite(TestSuite $suite): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function endTestSuite(TestSuite $suite): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function startTest(Test $test): void
    {
        BypassFinals::enable();
    }

    /**
     * {@inheritdoc}
     */
    public function endTest(Test $test, $time): void
    {
        // TODO: Implement endTest() method.
    }
}

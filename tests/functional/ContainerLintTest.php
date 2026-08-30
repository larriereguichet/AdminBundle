<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Functional;

use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\ApplicationTester;

/**
 * Booting the kernel is not enough to catch a service wired with the wrong type: the container only
 * checks an argument when it actually instantiates the service, so a definition nothing exercises can
 * stay broken for a long time. lint:container walks every definition and compares the declared types,
 * which is how a LinkRenderer receiving an OperationUrlGenerator went unnoticed here while it broke
 * every page of an application rendering an item link.
 */
final class ContainerLintTest extends KernelTestCase
{
    #[Test]
    public function itWiresEveryServiceWithTheTypeItDeclares(): void
    {
        $application = new Application(self::bootKernel());
        $application->setAutoExit(false);

        $tester = new ApplicationTester($application);
        $status = $tester->run(['command' => 'lint:container']);

        self::assertSame(0, $status, $tester->getDisplay());
    }
}

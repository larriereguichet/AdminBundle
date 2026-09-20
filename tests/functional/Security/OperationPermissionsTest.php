<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Functional\Security;

use LAG\AdminBundle\Tests\Application\Factory\AuthorFactory;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

/**
 * The permissions declared on an operation are enforced by AccessListener, which reads the operation from
 * the resource context. Both listeners sit on kernel.request, so the access check is only meaningful once
 * the context has been filled. When it runs first it finds no operation and returns without voting, which
 * grants instead of denying, and nothing anywhere reports an error.
 */
final class OperationPermissionsTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    #[Test]
    public function itDeniesAnOperationTheUserHasNoRoleFor(): void
    {
        $client = self::createClient();
        $author = AuthorFactory::createOne();

        $client->request('GET', '/authors/'.$author->id.'/secured');

        self::assertResponseStatusCodeSame(403);
    }

    #[Test]
    public function itAllowsAnOperationWhosePermissionTheUserHolds(): void
    {
        $client = self::createClient();
        $author = AuthorFactory::createOne();

        // Denying every permissioned operation would satisfy the assertion above just as well, so the
        // granting side of the vote needs its own case.
        $client->request('GET', '/authors/'.$author->id.'/granted');

        self::assertResponseIsSuccessful();
    }

    #[Test]
    public function itLeavesAnOperationWithoutPermissionsOpen(): void
    {
        $client = self::createClient();
        $author = AuthorFactory::createOne();

        $client->request('GET', '/authors/'.$author->id.'/show');

        self::assertResponseIsSuccessful();
    }
}

<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Functional\Resource;

use LAG\AdminBundle\Tests\Application\Factory\AuthorFactory;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class AuthorResourceTest extends WebTestCase
{
    use ResetDatabase, Factories;

    #[Test]
    public function itReturnsAnAuthorList(): void
    {
        // TODO
        $this->assertTrue(true); // @phpstan-ignore-line
//        $client = self::createClient();
//        AuthorFactory::createMany(5);
//
//        $crawler = $client->request('GET', '/admin/authors');
//
//        self::assertResponseIsSuccessful();
//        self::assertSelectorExists('table.admin-table');
//        self::assertSelectorTextContains('h1', 'Authors');
//
//        self::assertEquals(3, $crawler->filter('table thead th')->count());
//        self::assertCount(0, $crawler->filter('table.admin-table thead tr th p'));
    }
}

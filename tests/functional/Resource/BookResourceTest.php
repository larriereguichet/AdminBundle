<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Functional\Resource;

use LAG\AdminBundle\Tests\Application\Factory\BookFactory;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class BookResourceTest extends WebTestCase
{
    use ResetDatabase, Factories;

    #[Test]
    public function testIndex(): void
    {
        $client = self::createClient();
        $books = BookFactory::createMany(5);

        $crawler = $client->request('GET', '/books');

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('table.admin-table');
        self::assertSelectorTextContains('h3', 'Books');

        self::assertEquals('book.id', $crawler->filter('table.admin-table thead tr th')->eq(0)->text());
        self::assertEquals('book.name', $crawler->filter('table.admin-table thead tr th')->eq(1)->text());
        self::assertEquals('book.isbn', $crawler->filter('table.admin-table thead tr th')->eq(2)->text());
        self::assertEquals('', $crawler->filter('table.admin-table thead tr th')->eq(3)->text());

        self::assertCount(5, $crawler->filter('table.admin-table tbody tr'));

        self::assertEquals($books[0]->id, $crawler->filter('table.admin-table tbody tr td')->eq(0)->text());
        self::assertEquals($books[0]->name, $crawler->filter('table.admin-table tbody tr td')->eq(1)->text());
        self::assertEquals($books[0]->isbn, $crawler->filter('table.admin-table tbody tr td')->eq(2)->text());

        self::assertEquals($books[1]->id, $crawler->filter('table.admin-table tbody tr td')->eq(4)->text());
        self::assertEquals($books[1]->name, $crawler->filter('table.admin-table tbody tr td')->eq(5)->text());
        self::assertEquals($books[1]->isbn, $crawler->filter('table.admin-table tbody tr td')->eq(6)->text());
    }

    public function testShowLatest(): void
    {
        $client = self::createClient();
        $books = BookFactory::createMany(5);
        $client->request('GET', '/books/latest');

        self::assertResponseIsSuccessful();
    }
}
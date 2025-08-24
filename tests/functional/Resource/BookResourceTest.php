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
        // TODO
        $this->assertTrue(true); // @phpstan-ignore-line

//        $client = self::createClient();
//        $books = BookFactory::createMany(5);
//
//        $crawler = $client->request('GET', '/books');
//
//        self::assertResponseIsSuccessful();
//        self::assertSelectorExists('table.admin-table');
//        self::assertSelectorTextContains('h3', 'Books');
//
//        self::assertEmpty($crawler->filter('table.admin-table thead tr th')->eq(0)->text());
//        self::assertEquals('book.name', $crawler->filter('table.admin-table thead tr th')->eq(1)->text());
//        self::assertEquals('book.isbn', $crawler->filter('table.admin-table thead tr th')->eq(2)->text());
//        self::assertEquals('actions', $crawler->filter('table.admin-table thead tr th')->eq(3)->text());
//        self::assertEquals('', $crawler->filter('table.admin-table thead tr th')->eq(4)->text());
//
//        self::assertCount(0, $crawler->filter('table.admin-table thead tr th p'));
//
//        self::assertCount(5, $crawler->filter('table.admin-table tbody tr'));
//
//
//        for ($i = 1; $i <= 5; $i++){
//            $row = $crawler->filter('table.admin-table tbody tr')->eq($i - 1);
//            $book = $books[$i - 1];
//
//            self::assertEquals($book->id, $row->filter('td')->eq(0)->text());
//            self::assertEquals('/books/'.$book->id.'/show', $row->filter('td')->eq(0)->filter('a')->attr('href'));
//            self::assertEquals((string) $book->id, $row->filter('td')->eq(0)->filter('a')->text());
//            self::assertEquals($book->name, $row->filter('td')->eq(1)->text());
//            self::assertEquals($book->isbn, $row->filter('td')->eq(2)->text());
//            self::assertEquals('Show book', $row->filter('td')->eq(3)->text());
//            self::assertEquals('/books/'.$book->id.'/show', $row->filter('td')->eq(3)->filter('a')->attr('href'));
//
//        }
    }

    public function testShowLatest(): void
    {
        $client = self::createClient();
        $books = BookFactory::createMany(5);
        $client->request('GET', '/books/latest');

        self::assertResponseIsSuccessful();
    }
}

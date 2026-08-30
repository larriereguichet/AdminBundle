<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Functional\Grid;

use LAG\AdminBundle\Tests\Application\Factory\AuthorFactory;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

/**
 * A grid with no row has to render its table and its empty message. The index template used to read the
 * grid through the "default" filter, which replaces an empty value and not only an undefined one: a
 * GridView being traversable, an empty grid was replaced by false and the whole grid block was skipped,
 * so nothing was displayed at all and the empty message could never be reached.
 */
final class EmptyGridRenderingTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    #[Test]
    public function itRendersTheTableOfAGridWithoutRow(): void
    {
        $client = self::createClient();

        $crawler = $client->request('GET', '/authors/index');

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('table.admin-table');
        self::assertGreaterThan(0, $crawler->filter('table.admin-table thead th')->count());
    }

    #[Test]
    public function itRendersTheEmptyMessageOfAGridWithoutRow(): void
    {
        $client = self::createClient();

        $crawler = $client->request('GET', '/authors/index');

        $rows = $crawler->filter('table.admin-table tbody tr');

        self::assertCount(1, $rows);
        self::assertNotEmpty($rows->filter('td')->attr('colspan'));
        self::assertNotEmpty(trim($rows->filter('td')->text()));
    }

    #[Test]
    public function itStillRendersTheRowsOfAGridWithData(): void
    {
        $client = self::createClient();
        AuthorFactory::createMany(3);

        $crawler = $client->request('GET', '/authors/index');

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('table.admin-table');
        self::assertCount(3, $crawler->filter('table.admin-table tbody tr'));
    }
}

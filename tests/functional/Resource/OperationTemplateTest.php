<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Functional\Resource;

use LAG\AdminBundle\Tests\Application\Factory\AuthorFactory;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

/**
 * Covers the operations that fall back on the default @LAGAdmin/resources/operation.html.twig template, which no
 * test exercised: its cancel button reads an accessor on the index operation, and referenced a "fullName" that
 * exists nowhere in the metadata, so every such page answered a 500.
 */
final class OperationTemplateTest extends WebTestCase
{
    use ResetDatabase, Factories;

    #[Test]
    public function itRendersACreateOperation(): void
    {
        $client = self::createClient();

        $crawler = $client->request('GET', '/authors/create');

        self::assertResponseIsSuccessful();
        self::assertStringEndsWith('/authors/index', (string) $crawler->filter('a.btn-light')->attr('href'));
    }

    #[Test]
    public function itRendersAnUpdateOperation(): void
    {
        $client = self::createClient();
        $author = AuthorFactory::createOne();

        $crawler = $client->request('GET', '/authors/'.$author->id.'/update');

        self::assertResponseIsSuccessful();
        self::assertStringEndsWith('/authors/index', (string) $crawler->filter('a.btn-light')->attr('href'));
    }
}

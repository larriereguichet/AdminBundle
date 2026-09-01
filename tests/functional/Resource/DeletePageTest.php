<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Functional\Resource;

use LAG\AdminBundle\Tests\Application\Factory\AuthorFactory;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

/**
 * Nothing used to render the delete confirmation page, and it accumulated two defects because of it: the
 * only call site of the link renderer, wired with a url generator its constructor refuses, and a template
 * reading operation.itemActions after the accessor became getItemLinks(). Both raised a 500 on a page of
 * the base CRUD without a single test noticing.
 */
final class DeletePageTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    #[Test]
    public function itRendersTheDeleteConfirmationPage(): void
    {
        $client = self::createClient();
        $author = AuthorFactory::createOne();

        $crawler = $client->request('GET', '/authors/'.$author->id.'/delete');

        self::assertResponseIsSuccessful();
        self::assertGreaterThan(0, $crawler->filter('form')->count());
        self::assertGreaterThan(0, $crawler->filter('button')->count());
    }

    #[Test]
    public function itRendersTheItemLinksOfTheOperation(): void
    {
        $client = self::createClient();
        $author = AuthorFactory::createOne();

        $client->request('GET', '/authors/'.$author->id.'/delete');

        // The template reads the item links through the renderer: an accessor renamed on the operation, or a
        // renderer given the wrong url generator, both surface here and nowhere else.
        self::assertResponseIsSuccessful();
        self::assertSelectorExists('.btn-group');
    }
}

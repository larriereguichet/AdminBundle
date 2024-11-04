<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Functional\Resource;

use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class BookResourceTest extends WebTestCase
{
    #[Test]
    public function testIndex(): void
    {
        $client = static::createClient();
        $client->request('GET', '/books');

        self::assertResponseIsSuccessful();
    }
}
<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Functional\Resource;

use LAG\AdminBundle\Tests\Application\Factory\AuthorFactory;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

/**
 * A rich text property is stored as the HTML produced by the editor. The whole chain is only exercised through
 * a real request: the widget binds Trix to a hidden input, and the markup is sanitized by the form extension
 * before it reaches the entity.
 */
final class RichTextPropertyTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    #[Test]
    public function itBindsTheEditorToAHiddenInputHoldingTheValue(): void
    {
        $client = self::createClient();
        $author = AuthorFactory::createOne(['biography' => '<div>A <strong>novelist</strong>.</div>']);

        $crawler = $client->request('GET', '/authors/'.$author->id.'/update');

        self::assertResponseIsSuccessful();
        $input = $this->richTextInput($crawler);

        self::assertSame('<div>A <strong>novelist</strong>.</div>', $input->attr('value'));
    }

    #[Test]
    public function itStoresTheSubmittedMarkupWithoutItsUnsafeParts(): void
    {
        $client = self::createClient();
        $crawler = $client->request('GET', '/authors/create');
        self::assertResponseIsSuccessful();

        $form = $crawler->filter('form')->form();
        $biography = (string) $this->richTextInput($crawler)->attr('name');
        $form[str_replace('[biography]', '[name]', $biography)] = 'Jane Austen';
        $form[$biography] = '<div>A <strong>novelist</strong>.</div><script>alert(1)</script>';

        $client->submit($form);

        $author = AuthorFactory::repository()->findOneBy(['name' => 'Jane Austen']);

        self::assertNotNull($author);
        self::assertSame('<div>A <strong>novelist</strong>.</div>', $author->biography);
    }

    private function richTextInput(Crawler $crawler): Crawler
    {
        $editor = $crawler->filter('trix-editor');
        self::assertCount(1, $editor);

        $input = $crawler->filter('input[type="hidden"]#'.$editor->attr('input'));
        self::assertCount(1, $input);

        return $input;
    }
}

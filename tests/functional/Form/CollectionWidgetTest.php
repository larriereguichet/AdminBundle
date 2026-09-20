<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Functional\Form;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestWith;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormFactoryInterface;
use Twig\Environment;

/**
 * The collection controller substitutes __name__ with data-index-value and only then increments it, so the
 * attribute holds the next index rather than a count. Rendered entries are numbered with loop.index0, which
 * used to leave a hole between the last one and the prototype.
 */
final class CollectionWidgetTest extends KernelTestCase
{
    #[Test]
    #[TestWith([0])]
    #[TestWith([1])]
    #[TestWith([3])]
    public function itNumbersTheNextEntryAfterTheLastRenderedOne(int $count): void
    {
        $html = $this->renderCollection(array_fill(0, $count, 'tag'));

        self::assertStringContainsString(\sprintf('data-index-value="%d"', $count), $html);
    }

    #[Test]
    public function itNumbersTheRenderedEntriesFromZero(): void
    {
        $html = $this->renderCollection(['first', 'second']);

        self::assertStringContainsString('data-form-collection-index="0"', $html);
        self::assertStringContainsString('data-form-collection-index="1"', $html);
        self::assertStringNotContainsString('data-form-collection-index="2"', $html);
    }

    #[Test]
    public function itRendersTheEntryThroughTheItemBlock(): void
    {
        $html = $this->renderCollection(['first']);

        self::assertStringContainsString('btn btn-sm btn-outline-danger collection-button-remove', $html);

        // glyphicon and text-right are Bootstrap 3 class names: they render nothing at all in the
        // Bootstrap 5 layout this theme extends, so the delete button used to have no icon and no
        // alignment.
        self::assertStringNotContainsString('glyphicon', $html);
        self::assertStringNotContainsString('text-right', $html);
        self::assertStringNotContainsString('<br/>', $html);
    }

    #[Test]
    public function itCarriesTheItemPrototypeEscapedExactlyOnce(): void
    {
        $html = $this->renderCollection([]);

        self::assertStringContainsString('data-prototype-value="&lt;div data-form-collection=&quot;item&quot;', $html);
        self::assertStringContainsString('__name__', $html);
        self::assertStringNotContainsString('&amp;lt;', $html);
    }

    #[Test]
    public function itLetsAFormThemeOverrideTheItemBlock(): void
    {
        $html = $this->renderCollection(['first'], <<<'TWIG'
            {% form_theme form 'forms/collection_item_override.html.twig' %}
            {{ form_widget(form.tags) }}
            TWIG);

        // The whole point of turning the macro into a block: a macro cannot be overridden by a theme.
        // The override derives the index from the entry itself, which is the documented contract: it
        // replaces the whole block, so nothing the default body computes is in scope for it.
        self::assertStringContainsString('<p class="overridden">0</p>', $html);
        self::assertStringNotContainsString('collection-box', $html);
    }

    /**
     * @param list<string> $entries
     */
    private function renderCollection(array $entries, string $template = '{{ form_widget(form.tags) }}'): string
    {
        self::bootKernel();
        /** @var FormFactoryInterface $formFactory */
        $formFactory = self::getContainer()->get('form.factory');
        /** @var Environment $twig */
        $twig = self::getContainer()->get('twig');

        $form = $formFactory
            ->createBuilder(FormType::class, ['tags' => $entries], ['csrf_protection' => false])
            ->add('tags', CollectionType::class, ['allow_add' => true, 'allow_delete' => true])
            ->getForm()
        ;

        return $twig
            ->createTemplate($template)
            ->render(['form' => $form->createView()])
        ;
    }
}

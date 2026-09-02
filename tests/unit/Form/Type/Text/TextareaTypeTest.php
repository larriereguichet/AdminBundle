<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Form\Type\Text;

use LAG\AdminBundle\Form\Type\Text\TextareaType;
use LAG\AdminBundle\Tests\Unit\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TextareaTypeTest extends TestCase
{
    #[Test]
    public function itSanitizesTheSubmittedMarkup(): void
    {
        $options = $this->resolve();

        self::assertTrue($options['sanitize_html']);
        self::assertSame('lag_admin_rich_text', $options['sanitizer']);
    }

    #[Test]
    public function itDoesNotEnableAttachmentsByDefault(): void
    {
        self::assertNotContains('attachFiles', $this->resolve()['toolbar']);
    }

    #[Test]
    public function itAcceptsABooleanToolbar(): void
    {
        self::assertFalse($this->resolve(['toolbar' => false])['toolbar']);
    }

    #[Test]
    public function itRejectsAToolbarThatIsNotAListOfButtons(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $this->resolve(['toolbar' => [['bold']]]);
    }

    #[Test]
    public function itExposesTheToolbarAsJsonToTheWidget(): void
    {
        $view = new FormView();

        new TextareaType()->buildView($view, self::createStub(FormInterface::class), ['toolbar' => ['bold']]);

        self::assertSame('["bold"]', $view->vars['toolbar']);
    }

    /**
     * @param array<string, mixed> $options
     *
     * @return array<string, mixed>
     */
    private function resolve(array $options = []): array
    {
        $resolver = new OptionsResolver();
        new TextareaType()->configureOptions($resolver);

        return $resolver->resolve($options);
    }
}

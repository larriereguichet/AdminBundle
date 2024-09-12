<?php

declare(strict_types=1);

namespace LAG\AdminBundle\View\Helper;

use LAG\AdminBundle\Bridge\QuillJs\Render\QuillJsRendererInterface;
use Symfony\Component\String\Inflector\EnglishInflector;

final readonly class TextHelper implements TextHelperInterface
{
    public function __construct(
        private QuillJsRendererInterface $quillJsRenderer,
    ) {
    }

    public function pluralize(string $singular): string
    {
        return (new EnglishInflector())->pluralize($singular)[0];
    }

    public function richText(string $richText): string
    {
        return $this->quillJsRenderer->render($richText);
    }
}

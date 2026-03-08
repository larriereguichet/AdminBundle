<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Bridge\QuillJs\Render;

use LAG\AdminBundle\RichText\RichTextRendererInterface;
use nadar\quill\Lexer;

final readonly class QuillJsRenderer implements RichTextRendererInterface
{
    public function render(string $json): string
    {
        if (!json_validate($json)) {
            $json = json_encode([['insert' => $json]]);
        }

        return new Lexer($json)->render();
    }
}

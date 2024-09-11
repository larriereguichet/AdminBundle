<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Bridge\QuillJs\Render;

use nadar\quill\Lexer;

final readonly class QuillJsRenderer implements QuillJsRendererInterface
{
    public function render(array|string $json): string
    {
        if (!json_validate($json)) {
            $json = json_encode([['insert' => $json]]);
        }

        return (new Lexer($json))->render();
    }
}

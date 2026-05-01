<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Twig\Runtime;

use LAG\AdminBundle\RichText\RichTextRendererInterface;
use Twig\Extension\RuntimeExtensionInterface;

final readonly class RichTextRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private RichTextRendererInterface $quillJsRenderer,
    ) {
    }

    public function renderRichText(?string $richText): string
    {
        if ($richText === null) {
            return '';
        }

        return $this->quillJsRenderer->render($richText);
    }
}

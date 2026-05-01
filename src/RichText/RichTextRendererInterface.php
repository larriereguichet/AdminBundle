<?php

declare(strict_types=1);

namespace LAG\AdminBundle\RichText;

interface RichTextRendererInterface
{
    /**
     * Render rich text value from JSON text value to HTML value.
     *
     * @param string $json JSON stored rich text
     *
     * @return string HTML rich text
     */
    public function render(string $json): string;
}

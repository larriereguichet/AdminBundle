<?php

namespace LAG\AdminBundle\View\Component\Cell;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use function Symfony\Component\String\u;

#[AsTwigComponent(
    name: 'lag_admin:cell:text',
    template: '@LAGAdmin/components/grids/cells/text.html.twig',
)]
class Text
{
    public mixed $text;
    public bool $translation = true;
    public ?string $translationDomain = null;
    public array $translationParameters = [];
    public bool $displayHtmlElement = true;

    public function mount(
        mixed $data,
        ?string $prefix = null,
        ?string $suffix = null,
        ?int $length = null,
        ?string $empty = null,
        ?string $replace = null,
        bool $stripTags = true,
    ): void {
        if ($data === null) {
            $data = '';
        }
        $data = u((string)$data);

        if ($length && $replace) {
            $data = $data->truncate($length)->append($replace);
        }

        if ($prefix) {
            $data = $data->prepend($prefix);
        }

        if ($suffix) {
            $data = $data->append($suffix);
        }

        if ($data->length() === 0 && $empty) {
            $data = $data->append($empty);
        }

        if ($stripTags) {
            $data = u(strip_tags($data->toString()));
        }
        $this->text = $data->toString();
    }
}

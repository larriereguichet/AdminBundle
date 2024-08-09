<?php

declare(strict_types=1);

namespace LAG\AdminBundle\View\Component\Cell;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'lag_admin:link',
    template: '@LAGAdmin/components/cells/link.html.twig',
)]
class Link
{
    public string $url;
    public string $text;
    public ?string $icon = null;
    public bool $translation = true;
    public ?string $translationDomain = null;
    public array $translationParameters = [];
    public ?string $prefix = null;
    public ?string $suffix = null;
    public ?int $length = null;

    public function mount(mixed $data, ?string $text = null): void
    {
        $this->url = $data;

        if ($text === null) {
            $this->text = $this->url;
        } else {
            $this->text = $text;
        }
    }
}

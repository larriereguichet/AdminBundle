<?php

namespace LAG\AdminBundle\View\Component\Cell;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'lag_admin:cell:link',
    template: '@LAGAdmin/components/cells/link.html.twig',
)]
class Link
{
    public mixed $text;
    public bool $translation = true;
    public ?string $translationDomain = null;
    public array $translationParameters = [];
    public ?string $prefix = null;
    public ?string $suffix = null;
    public ?int $length = null;

    public function mount(mixed $data): void
    {

    }
}

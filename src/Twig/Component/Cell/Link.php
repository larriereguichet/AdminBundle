<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Twig\Component\Cell;

use LAG\AdminBundle\Grid\View\CellView;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'lag_admin:link',
    template: '@LAGAdmin/components/cells/link.html.twig',
)]
class Link
{
    public ?string $url = null;
    public ?string $text = null;
    public ?string $icon = null;
    public bool $translation = true;
    public ?string $translationDomain = null;
    /** @var array<string, mixed> */
    public array $translationParameters = [];
    public ?string $prefix = null;
    public ?string $suffix = null;
    public ?int $length = null;

    public function mount(CellView $cell): void
    {
        $this->url = \is_string($cell->data) ? $cell->data : '';

        if ($cell->label !== null) {
            $this->text = $cell->label;
        } else {
            $this->text = $this->url;
        }
    }
}

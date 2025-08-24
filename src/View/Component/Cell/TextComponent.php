<?php

declare(strict_types=1);

namespace LAG\AdminBundle\View\Component\Cell;

use LAG\AdminBundle\Grid\View\CellView;
use LAG\AdminBundle\Metadata\Text;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

use function Symfony\Component\String\u;

// TODO remove ?
#[AsTwigComponent(
    name: 'lag_admin:text',
    template: '@LAGAdmin/components/cells/text.html.twig',
)]
class TextComponent
{
    public mixed $text;
    public bool $translation = true;
    public ?string $translationDomain = null;
    public array $translationParameters = [];
    public bool $displayHtmlElement = true;

    public function mount(
        mixed $data,
        CellView $cell,
    ): void {
        if ($data === null) {
            $data = '';
        }
        /** @var Text $property */
        $property = $cell->options;
        $data = u((string) $data);

        if ($property->getLength() && $property->getReplace() && $data->length() > $property->getLength()) {
            $data = $data->truncate($property->getLength())->append($property->getReplace());
        }

        if ($property->getPrefix()) {
            $data = $data->prepend($property->getPrefix());
        }

        if ($property->getSuffix()) {
            $data = $data->append($property->getSuffix());
        }

        if ($data->length() === 0 && $property->getEmpty()) {
            $data = $data->append($property->getEmpty());
        }
        $this->text = $data->toString();
    }
}
